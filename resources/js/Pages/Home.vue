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
const selectedTracksForPlaylist = ref([]);
const volume = ref(20);
const referenceSongs = ref([]);
const playlistName = ref('');

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
    const response = await axios.post('/spotify/createPlaylist', {
        params: { 
            name: playlistName.value,
            tracks_ids: selectedTracksForPlaylist.value.map(track => 'spotify:track:' + track.id),
        }
    });
    searchResults.value = response.data;
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
      dragToSeek: true
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

</script>

<template>
  <Header :user="props.user" v-model:recommendations="recommendations" v-model:isSeaching="isSeaching" @update:referenceSongs="updateReferenceSongs" isHome>
    <template #content>
      <div class="row h-100" style="padding: 0rem 17vw 0rem 17vw">
        <div style="background-color: rgb(96, 129, 150, 0.4);border-radius: 22px;padding: 1rem; min-height: 78vh; box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19)">
          <ul class="nav nav-tabs mb-3 mx-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab" aria-controls="search" aria-selected="true">Search for Tracks</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="recommendation-tab" data-bs-toggle="tab" data-bs-target="#recommendation" type="button" role="tab" aria-controls="recommendation" aria-selected="false">Recommendations</button>
            </li>
          </ul>
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="search" role="tabpanel" aria-labelledby="search-tab">
              <div class="col-12" style="height: 64vh">
  
                <form @submit.prevent="submitForm" class="d-flex align-items-end ">
                  <div class="ms-3">
                    <!-- <label for="query" style="color: #f7f9fb; font-weight: 600;">Search Song</label> -->
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
                </form>

                <SongList
                  :isSearchingSongs="isSearchingSongs"
                  :songs="searchResults"
                  v-model:referenceSongs="referenceSongs"
                  :searchGenre="searchGenre"
                >
                </SongList>

              </div>
            </div>
            <div class="tab-pane fade" id="recommendation" role="tabpanel" aria-labelledby="recommendation-tab">
              <div class="col-12" style="height: 64vh">
                  <div class="d-flex align-items-end">
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
                        :disabled="playlistName == '' || recommendations == null || selectedTracksForPlaylist.length == 0" 
                        @click="createPlaylist()" 
                      />
                    </div>
                  </div>

                  <SongList
                    :isSearchingSongs="isSearchingSongs"
                    :songs="recommendations"
                    v-model:referenceSongs="referenceSongs"
                    :searchGenre="searchGenre"
                    isRecommendations
                    v-model:selectedTracksForPlaylist="selectedTracksForPlaylist"
                  >
                  </SongList>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Header>
</template>

<style scoped>
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