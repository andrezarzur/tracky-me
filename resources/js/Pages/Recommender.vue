<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import SongList from '@/Components/SongList.vue';
import WaveSurfer from 'wavesurfer.js'
import PrimaryButton from '../Components/Buttons/PrimaryButton.vue';


const props = defineProps({
  user: Object
})

let wavesurfer = ref(null);
const query = ref('');
const searchResults = ref(null);
const searchType = ref('track');
const recommendations = ref(null);
const searchGenre = ref('');
const isSearchingSongs = ref(false);
const isSearching = ref(false);
const selectedTracksForPlaylist = ref([]);
const volume = ref(20);
const referenceSongs = ref([]);
const playlistName = ref('');
const uncheckedRecommendations = ref(false); 
const header = ref(null);
const isCreatingPlaylist = ref(false);

const submitForm = async () => {
  try {

    const searchString = searchType.value + ':' + query.value;

    isSearchingSongs.value = true;

    const response = await axios.get('/getSpotifyResults', {
        params: { 
            query: searchString,
        }
    });
    searchResults.value = response.data;

    isSearchingSongs.value = false;
  } catch (error) {
    console.error('There was an error fetching the results:', error);
  }
};

const createPlaylist = async () => {
  try {

    isCreatingPlaylist.value = true;

    const tracksWithoutReference = selectedTracksForPlaylist.value;

    if (includeReferenceTracks.value) {
      selectedTracksForPlaylist.value = [...referenceSongs.value, ...selectedTracksForPlaylist.value];
    }

    const response = await axios.post('/spotify/createPlaylist', {
        params: { 
            name: playlistName.value,
            tracks_ids: selectedTracksForPlaylist.value.map(track => 'spotify:track:' + track.id),
        }
    });
    searchResults.value = response.data;

    isCreatingPlaylist.value = false;

    if (response.status === 200) {
      header.value.triggerToast();
    }

    selectedTracksForPlaylist.value = tracksWithoutReference;

  } catch (error) {
    console.error('There was an error fetching the results:', error);
  }
}

const previewUrl = ref('');
const isPlaying = ref(false);

watch(previewUrl, () => {
  setTimeout(() => {
    wavesurfer = WaveSurfer.create({
      container: '#waveform',
      waveColor: '#A09ABC',
      progressColor: '#B6A6CA',
      url: previewUrl.value,
      cursorColor: 'none',
      height: 45,
      barWidth: 4,
      barGap: 2,
      barRadius: 2,
    })
    
    wavesurfer.on('interaction', () => {
      wavesurfer.play()
    })
    wavesurfer.setVolume(volume.value / 100);
    watch(volume, () => {
      wavesurfer.setVolume(volume.value / 100);
    })

    isPlaying.value = false;

  }, 1);
})

const updateReferenceSongs = (songs) => {
  referenceSongs.value = songs;
}

watch(recommendations, () => {
  if (recommendations !== null) {
    uncheckedRecommendations.value = true;
  } else {
    uncheckedRecommendations.value = false;
  }
})

const includeReferenceTracks = ref(false);

</script>

<template>
  <Header ref="header" :user="props.user" v-model:recommendations="recommendations" v-model:isSearching="isSearching" @update:referenceSongs="updateReferenceSongs" isHome>
    <template #content>
      <div class="row h-100 modal-padding" style="padding: 0rem 17vw 0rem 17vw">
        <div style="background:linear-gradient(180deg, #897351, #575b58 );border-radius: 22px;padding: 1rem; height: 80vh; box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19); display: flex; flex-direction: column;" class="modal-content">
          <ul class="nav nav-tabs mb-3 mx-3 justify-content-between nav-spacing" id="myTab" role="tablist">
            <div class="d-flex">
              <li class="nav-item" role="presentation">
                <button class="nav-link active nav-font-size" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab" aria-controls="search" aria-selected="true">Search</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link  position-relative nav-font-size" id="recommendation-tab" @click="uncheckedRecommendations = false" data-bs-toggle="tab" data-bs-target="#recommendation" type="button" role="tab" aria-controls="recommendation" aria-selected="false">
                  Recommendations
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" v-if="uncheckedRecommendations">
                    Here!
                    <span class="visually-hidden">unread messages</span>
                  </span>
                </button>
              </li>
            </div>
            <img class="mx-2 img-spacing" src="../../icons/Spotify_Logo_RGB_White.png" height="34" ></img>
          </ul>
          <div class="tab-content content-responsive" id="myTabContent" style="flex: 1; min-height: 0px;">
            <div class="tab-pane fade show active h-100" id="search" role="tabpanel" aria-labelledby="search-tab">
              <div class="col-12 h-100" style="display: flex; flex-direction: column;">
  
                <form @submit.prevent="submitForm" class="d-flex align-items-end justify-content-between">
                  <div class="d-flex">
                    <div class="ms-3">
                      <input 
                        type="text" 
                        v-model="query" 
                        id="query" 
                        required 
                        class="form-control" 
                        placeholder="Track or Artist"
                        aria-label="Small" 
                        aria-describedby="inputGroup-sizing-sm"
                        style="font-weight: 500; color:#31708e;"
                        @click="click()"
                      >
                    </div>
                    <div class="d-flex align-items-end">
                      <div class="ms-3">
                        <PrimaryButton
                          label="Search"
                          type="submit"
                        />
                      </div>
                      <span class="ms-3" style="color: #f7f9fb; font-weight: 600">by</span>
                      <div class="ms-3">
                        <select class="form-select simple-dropdown" aria-label="Default select example" v-model="searchType" style="color: #31708e; font-weight: 500">
                          <option value="track" selected>Track</option>
                          <option value="artist">Artist</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </form>

                <SongList
                  :isSearchingSongs="isSearchingSongs"
                  :songs="searchResults"
                  v-model:referenceSongs="referenceSongs"
                  :searchGenre="searchGenre"
                >
                </SongList>

                <div v-if="searchResults" class="me-3 mt-3 d-flex justify-content-end">
                  <span style="color: #f7f9fb; font-weight: 600">
                   Showing {{ searchResults.tracks.items.length }} results
                  </span>
                </div>

              </div>
            </div>
            <div class="tab-pane fade h-100" id="recommendation" role="tabpanel" aria-labelledby="recommendation-tab">
              <div class="col-12 h-100" style="display: flex; flex-direction: column;">
                  <div class="d-flex align-items-center">
                    <div class="ms-3">
                      <input 
                        type="text" 
                        class="form-control" 
                        aria-label="Small" 
                        aria-describedby="inputGroup-sizing-sm" 
                        placeholder="Playlist Name"
                        style="font-weight: 500; color:#31708e;"
                        v-model="playlistName"
                      >
                    </div>
                    <div class="ms-3">
                      <PrimaryButton
                        label="Create Playlist"
                        type="submit"
                        :loading="isCreatingPlaylist"
                        :disabled="playlistName == '' || recommendations == null || selectedTracksForPlaylist.length == 0" 
                        @click="createPlaylist()" 
                      />
                    </div>
                    <div class="ms-3 d-flex align-items-center h-100" v-if="recommendations">
                      <input class="form-check-input ms-3" type="checkbox" value="" v-model="includeReferenceTracks" style="margin-top: 0px">
                       <span style="color: #f7f9fb; font-weight: 500" class="ms-2">
                         Include reference tracks
                       </span>
                    </div>
                  </div>
                  <SongList
                    :isSearchingSongs="isSearching"
                    :songs="recommendations"
                    v-model:referenceSongs="referenceSongs"
                    :searchGenre="searchGenre"
                    isRecommendations
                    v-model:selectedTracksForPlaylist="selectedTracksForPlaylist"
                  >
                  </SongList>

                  <div v-if="recommendations" class="me-3 mt-3 d-flex justify-content-end">
                    <span style="color: #f7f9fb; font-weight: 600">
                     Showing {{ recommendations.tracks.length }} results
                    </span>
                  </div>
  
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Header>
</template>

<style scoped>
@media (max-width: 970px) {
  .modal-padding {
    padding: 0 !important;
    margin-right: 0px !important;
    margin-left: 0px !important;
  }  

  .modal-content {
    height: 100% !important;
    border-radius: 0 !important;
  }

  .nav-font-size {
    font-size: 20px !important;
  }

  .nav-spacing {
    justify-content: start !important;
  }

  .img-spacing {
    display: none;
  }
  

  .content-responsive {
    padding-bottom: 60px;
  }
}
.toggle-button {
  transition: transform 0.3s; /* Ensure smooth transition for all states */
}

.toggle-button:hover {
  transform: scale(1.1); /* Scale up on hover */
}

.toggle-button:active {
  transform: scale(1); /* Scale down on click */
  transition: 0.2s;
}

.sticky-header {
  position: sticky;
  top: 0;
  background-color: white; /* Ensure it has a background color to cover content underneath */
  z-index: 1000; /* Ensure it is above other content */
  border-radius: 0; /* Reset border-radius if any */
}

.track-row {
  background-color: #f7f9fb;
  margin: 1rem; 
  border: none;
  border-radius: 12px; 
  padding-top: 0.4rem; 
  padding-bottom: 0.4rem; 
  box-shadow: 0 5px 7px 0 rgba(0,0,0,0.2), 0 5px 7px 0 rgba(0,0,0,0.19);
}
</style>

<style css>
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
  color: #f3f3f3 !important;
  background-color: transparent !important;
  border-color: transparent transparent #f3f3f3 !important;
  border-bottom: 4px solid !important;
  font-size: 20px;
  font-weight: bold;
}

.nav-tabs .nav-link {
  border-top-left-radius: .25rem;
  border-top-right-radius: .25rem;
  color: #ffffff;
  font-size: 20px;
}
.nav-tabs .nav-link:hover {
  border: none;
  color: #31708e;
}
.nav-tabs .nav-link:not(:hover) {
  border: none;
}

.nav-tabs .nav-link:focus,
.nav-tabs .nav-link:focus-visible {
  border: none;
}


</style>