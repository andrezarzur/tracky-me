<script setup>
import { ref, watch, defineModel } from 'vue';
import WaveSurfer from 'wavesurfer.js'
import PrimaryButton from './Buttons/PrimaryButton.vue';

const props = defineProps({
    songs: Object,
    isSearchingSongs: Boolean,
    searchGenre: String,
    isRecommendations: Boolean,
    isPlaylist: Boolean
})

const selectedTracksForPlaylist = defineModel('selectedTracksForPlaylist', {
  default: []
});


let wavesurfer = ref(null);
const referenceSongs = defineModel('referenceSongs');
const searchGenre = ref(props.searchGenre);

const previewUrl = ref('');
const volume = ref(20);
const isPlaying = ref(false);

const playPreview = (preview_url) => {
  if (preview_url && preview_url !== previewUrl.value) {
    previewUrl.value = preview_url;
  } else if(preview_url === previewUrl.value) {
    play();
  }
}

watch(previewUrl, () => {
  setTimeout(() => {
    wavesurfer = WaveSurfer.create({
      container: '#waveform',
      waveColor: '#688F85',
      progressColor: '#264F44',
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

    wavesurfer.on('ready', () => {
      wavesurfer.play()
    })

    isPlaying.value = true;

  }, 1);
})


const toggleTrack = (track) => {
  const index = referenceSongs.value.findIndex(t => t.id === track.id);
  if (index === -1) {
    referenceSongs.value.push(track);
    saveToLocalStorage();
  } else {
    referenceSongs.value.splice(index, 1);
    saveToLocalStorage();
  }
};

const saveToLocalStorage = () => {
    localStorage.setItem('recc', JSON.stringify(referenceSongs.value));
}

const isSelectionFull = () => {
    const isGenreSelected = searchGenre.value !== '' ? 1 : 0;
    const totalTracks = referenceSongs.value.length;
    return totalTracks + isGenreSelected >= 5;
}

function msToTime(duration) {
    let seconds = Math.floor((duration / 1000) % 60)
    let minutes = Math.floor((duration / (1000 * 60)) % 60)
    let hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    seconds = (seconds < 10) ? "0" + seconds : seconds;

    return hours + ":" + minutes + ":" + seconds;
}

const isTrackSelected = (track) => {
    return referenceSongs.value.findIndex(t => t.id === track.id) !== -1;
}

const isTrackSelectedForPlaylist = (track) => {
    return selectedTracksForPlaylist.value.findIndex(t => t.id === track.id) !== -1;
}

const toggleTrackForPlaylist = (track) => {
  const index = selectedTracksForPlaylist.value.findIndex(t => t.id === track.id);
  if (index === -1) {
    selectedTracksForPlaylist.value.push(track);
  } else {
    selectedTracksForPlaylist.value.splice(index, 1);
  }
};

const toggleAll = () => {
  if (selectedTracksForPlaylist.value.length == props.songs.tracks.length) {
    selectedTracksForPlaylist.value = [];
  } else {
    selectedTracksForPlaylist.value = [...props.songs.tracks];
  }
}

const play = () => {
  if (isPlaying.value) {
    wavesurfer.pause();
  } else {
    wavesurfer.play();
  }
  isPlaying.value = !isPlaying.value;
};

const resetPlayer = () => {
  previewUrl.value = '';
  isPlaying.value = false;
}

</script>

<template>
    <div v-if="songs || isSearchingSongs" class="row sticky-header align-items-center" style=" margin-right: 1rem; margin-left: 1rem; min-height: 40px; background-color: transparent; margin-top: 1rem;">
      <div class="col-3 d-flex justify-content-center" style="color: #f7f9fb; font-weight: 600">
            <PrimaryButton
            v-if="isRecommendations"
            label="Toggle All"
            type="submit"
            @click="toggleAll()" 
          />
        </div>
        <div class="col-2 hide" style="color: #f7f9fb; font-weight: 600">Artist</div>
        <div class="col-4 col-hidden" style="color: #f7f9fb; font-weight: 600">Name</div>
        <div class="col-2 hide" style="color: #f7f9fb; font-weight: 600">Duration</div>
        <div class="col-1 col-hidden d-flex justify-content-center align-items-center" style="color: #f7f9fb; font-weight: 600">
          Preview
          <div class="ms-2" style="display: none;">
            <img class="toggle-button" src="../../icons/info-circle.svg" height="18" width="18">
            </img>
          </div>
        </div>
      </div>
      <div v-if="songs && !isSearchingSongs" style="flex: 1; overflow-y: auto;">
        <div v-if="isPlaylist ? (songs == 0 ? true : false) : isRecommendations ? (songs['tracks'] == 0 ? true : false) : (songs['tracks']['items'] == 0 ? true : false)" class="d-flex justify-content-center align-items-center h-100">
          <span style="color: #f7f9fb; font-weight: 600; font-size: 20px">
            No tracks found
          </span>
        </div>
        <div class="row track-row" :style="isTrackSelected(result) ? 'background-color: #34897A' : ''" v-for="(result, index) in isPlaylist ? songs : isRecommendations ? songs['tracks'] : songs['tracks']['items']" :key="index">
          <div class="col-1 d-flex align-items-center justify-content-center force-col-2" >
            <div 
              class="toggle-button" 
              @click="toggleTrack(result)"
              :style="{ pointerEvents: isSelectionFull() && !isTrackSelected(result) ? 'none' : 'auto' }"
            >
            <img class="toggle-button" src="../../icons/plus_disabled.svg" height="34" width="34" v-if="isSelectionFull() && !isTrackSelected(result)" style="cursor: pointer"></img>
              <img class="toggle-button" src="../../icons/plus.svg" height="34" width="34" v-else-if="!isTrackSelected(result)" style="cursor: pointer"></img>
              <img class="toggle-button"  src="../../icons/dash.svg" height="34" width="34" v-else style="cursor: pointer"></img>
            </div>
            <input v-if="isRecommendations" class="form-check-input ms-3" type="checkbox" value="" @change="toggleTrackForPlaylist(result)" :checked="isTrackSelectedForPlaylist(result)">
          </div>
          <div class="col-2 d-flex align-items-center force-col-3">
            <img :src="result.album['images'][0]['url']" height="60" width="60"/>
          </div>
          <div class="col-2 d-flex align-items-center hide">
            <span style="color: #292D2A;  font-weight: 500" :style="isTrackSelected(result) ? 'color: #f7f9fb; font-weight: 600' : ''">
              {{ result.artists[0]['name'] }}
            </span>
          </div>
          <div class="col-4 d-flex align-items-center force-col-5">
            <span style="color: #292D2A;  font-weight: 500" :style="isTrackSelected(result) ? 'color: #f7f9fb; font-weight: 600' : ''">
              {{ result.name }}
            </span>
          </div>
          <div class="col-2 d-flex align-items-center hide">
            <span style="color: #292D2A;  font-weight: 500" :style="isTrackSelected(result) ? 'color: #f7f9fb; font-weight: 600' : ''">
              {{ msToTime(result.duration_ms) }}
            </span>
          </div>
          <div class="col-1 d-flex align-items-center force-col-2">
            <div  @click="playPreview(result.preview_url)" >
              <img class="toggle-button" src="../../icons/play.svg" height="34" width="34" v-if="(result.preview_url && isPlaying && result.preview_url !== previewUrl && !isTrackSelected(result)) || result.preview_url && !isPlaying && !isTrackSelected(result)" style="cursor: pointer"></img>
              <img class="toggle-button" src="../../icons/play_white.svg" height="34" width="34" v-else-if="(result.preview_url && isPlaying && result.preview_url !== previewUrl) || result.preview_url && !isPlaying && isTrackSelected(result)" style="cursor: pointer"></img>
              <img class="toggle-button" src="../../icons/pause.svg" height="34" width="34" v-else-if="result.preview_url && isPlaying && result.preview_url === previewUrl && !isTrackSelected(result)" style="cursor: pointer"></img>
              <img class="toggle-button" src="../../icons/pause_white.svg" height="34" width="34" v-else-if="result.preview_url && isPlaying && result.preview_url === previewUrl && isTrackSelected(result)" style="cursor: pointer"></img>
              <img src="../../icons/play_disabled.svg" height="34" width="34" v-else></img>
            </div>
          </div>
          <div>
            <div v-if="previewUrl !== '' && previewUrl === result.preview_url" class="mt-3" style="background-color: #A9CFC5; padding: 1rem; border-radius: 1rem;">
              <div class="w-100 mb-3 d-flex justify-content-center align-items-center">
                <a :href="result.external_urls['spotify']" target="_blank" style="text-decoration: none; color: white;">
                  <span style="color: #f7f9fb;  font-weight: 500;">
                    See on
                  </span>
                  <img class="mx-2" src="../../icons/Spotify_Logo_RGB_White.png" height="28" ></img>
                </a>
              </div>
              <div class="row mx-2 align-items-center" >
                <div class="col-1 px-0">
                  <div class="btn btn-primary" style="background-color: transparent; border: none;" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="toggle-button" src="../../icons/volume-up.svg" height="34" width="34"></img>
                  </div>
                  <div class="dropdown-menu" style="width: 20vw; padding-left: 0.5rem; padding-right: 0.5rem">
                    <div class="h-100 d-flex align-items-center"> 
                      <input type="range" class="custom-range h-100" id="customRange1" v-model="volume" style="border-radius: 30px">
                    </div>
                  </div>
                </div>
                <div class="col-10">
                  <div id="waveform"></div>
                </div>
                <div class="col-1 px-0 d-flex align-items-center justify-content-center">
                  <button type="button" class="btn-close" @click="resetPlayer()" aria-label="Close"></button>  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else-if="isSearchingSongs" style="padding-top: 0.5rem; flex: 1; overflow-y: auto;">
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
        <h5 class="card-title placeholder-glow">
          <span class="placeholder col-12" style="height: 72px; border-radius: 12px;margin: 0.5rem; width: 98%"></span>
        </h5>
      </div>
      <div v-else class="d-flex justify-content-center align-items-center" style="height: 95%">
        <div class="toggle-button d-flex flex-column justify-content-center align-items-center" v-if="!isRecommendations">
          <img src="../../icons/no_search_green.svg" height="340" width="340" class="img-size"></img>
          <span style="color:  #f7f9fb;  font-weight: 600; font-size: 20px" class="img-text-size">Start by searching for a track</span>
        </div>
        <div class="toggle-button d-flex flex-column justify-content-center align-items-center h-100" v-else>
          <img src="../../icons/bird_green.svg" height="340" width="340" class="img-size"></img>
          <span style="color:  #f7f9fb;  font-weight: 600; font-size: 20px" class="img-text-size">Try using the recommendations builder</span>
        </div>
      </div>
</template>

<style scoped>

@media (max-width: 970px) {
  .img-size {
    height: 220px;
    width: 220px;
  }

  .img-text-size {
    font-size: 17px !important;
  }

  .hide {
    display: none !important;
  }

  .force-col-2 {
    width: 16.6% !important;
  }

  .force-col-3 {
    width: 25% !important;
  }

  .force-col-4 {
    width: 33.3% !important;
  }

  .force-col-5 {
    width: 41.6% !important;
  }
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

.custom-range {
  -webkit-appearance: none;
  background-color: #ddd;
  height: 20px;
  overflow: hidden;
  width: 100%;
}

.custom-range::-webkit-slider-runnable-track {
  -webkit-appearance: none;
  height: 20px;
}

.custom-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  background: #264F44;
  border-radius: 50%;
  box-shadow: -210px 0 0 200px #688F85;
  cursor: pointer;
  height: 20px;
  width: 20px;
  border: 0;
}

.custom-range::-moz-range-thumb {
  background: #333;
  border-radius: 50%;
  box-shadow: -1010px 0 0 1000px #666;
  cursor: pointer;
  height: 20px;
  width: 20px;
  border: 0;
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

input[type="range"]::-moz-range-track {
  background-color: #ddd;
}
input[type="range"]::-moz-range-progress {
  background-color: #666;
  height: 20px
}
input[type="range"]::-ms-fill-upper {
  background-color: #ddd;
}
input[type="range"]::-ms-fill-lower {
  background-color: #666;
}
</style>