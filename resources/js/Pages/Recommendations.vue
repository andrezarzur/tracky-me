<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import WaveSurfer from 'wavesurfer.js'
import PrimaryButton from '../Components/Buttons/PrimaryButton.vue';

let wavesurfer = ref(null);
const query = ref('');
const searchResults = ref(null);
const searchType = ref('track');
const recommendType = ref('track');
const recommendations = ref(null);
const searchGenre = ref('');
const recommendationAmount = ref(10);

const props = defineProps({
  user: Object,
  recommendations: Array
})

const selectedTracks = ref([]);
const selectedTracksForPlaylist = ref([]);
const volume = ref(20);
const referenceSongs = ref([]);

const playlistName = ref('');

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

const toggleTrackForPlaylist = (track) => {
  const index = selectedTracksForPlaylist.value.findIndex(t => t.id === track.id);
  if (index === -1) {
    selectedTracksForPlaylist.value.push(track);
  } else {
    selectedTracksForPlaylist.value.splice(index, 1);
  }
};

const previewUrl = ref('');

const playPreview = (preview_url) => {
  console.log(preview_url)
  if (preview_url) {
    previewUrl.value = preview_url;
  }
}

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

const play = () => {
  if (isPlaying.value) {
    wavesurfer.pause();
  } else {
    wavesurfer.play();
  }
  isPlaying.value = !isPlaying.value;
};
const isTrackSelected = (track) => {
    return selectedTracks.value.findIndex(t => t.id === track.id) !== -1;
}
const isTrackSelectedForPlaylist = (track) => {
    return selectedTracksForPlaylist.value.findIndex(t => t.id === track.id) !== -1;
}

const isSelectionFull = () => {
    const isGenreSelected = searchGenre.value !== '' ? 1 : 0;
    const totalTracks = selectedTracks.value.length;
    return totalTracks + isGenreSelected >= 5;
}

</script>

<template>
  <Header :user="props.user" v-model:referenceSongs="referenceSongs">
    <template #content>
      <div class="row h-100">
        <div class="col-6">
          <div class="vr" style="position: absolute; height: 90vh;"></div>
            <div>
              <div class="d-flex align-items-end">
                <div class="ms-3">
                  <span>Playlist Name</span>
                  <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" v-model="playlistName">
                </div>
                <div class="ms-3">
                  <PrimaryButton
                    label="Create Playlist"
                    type="submit"
                    :disabled="playlistName == '' || recommendations == null"
                    @click="createPlaylist()" 
                  />
                </div>
                <div class="ms-3">
                  <label for="customRange1" class="form-label">Max Amount</label>
                  <input type="range" class="form-range" id="customRange1" min="1" v-model="recommendationAmount">
                </div>
                <button 
                  class="btn btn-danger ms-3"
                  disabled
                >
                  {{recommendationAmount}}
                </button>
              </div>
              <div v-if="recommendations"  style="overflow-y: scroll; max-height: 82vh; margin-top: 1rem">
                <div>
                  <div class="card" v-for="recommendation in recommendations['tracks']" :key="recommendation.id" style="margin: 1rem; padding: 1rem">
                    <div class="row h-100">
                      <div class="col-3 d-flex align-items-center">
                        <input class="form-check-input me-3 ms-3" type="checkbox" value="" @change="toggleTrackForPlaylist(recommendation)" :checked="isTrackSelectedForPlaylist(recommendation)">
                        <img :src="recommendation.album['images'][0]['url']" height="85" width="85"/>
                      </div>
                      <div class="col-9 d-flex flex-column justify-content-center">
                        <div>
                          <button v-if="recommendation.preview_url" @click="playPreview(recommendation.preview_url)" class="btn btn-outline-dark">Play Preview</button>
                          <button v-else disabled class="btn btn-outline-dark">No Preview</button>
                        </div>
                        <div>
                          Artist: {{ recommendation.artists[0]['name'] }}
                        </div>
                        <div>
                          Track: {{ recommendation.name }}
                        </div>
                      </div>
                      <div v-if="previewUrl !== '' && previewUrl === recommendation.preview_url" class="mt-3" style="background-color: #D5CFE1; padding: 1rem; border-radius: 1rem;">
                        <div class="row mx-2 align-items-center" >
                          <div class="col-1 px-0">
                            <button class="btn btn-primary" @click="play()">P</button>
                          </div>
                          <div class="col-10">
                            <div id="waveform"></div>
                          </div>
                          <div class="col-1 px-0 d-flex align-items-center">
                            <button class="btn btn-primary" @click="previewUrl = ''">x</button>  
                          </div>
                          <!-- <label for="customRange1" class="form-label">Volume</label>
                          <input type="range" class="form-range" id="customRange1" v-model="volume"> -->
                        </div>
                      </div>
                    </div>
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
</style>