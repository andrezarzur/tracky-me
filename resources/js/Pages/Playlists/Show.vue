<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import WaveSurfer from 'wavesurfer.js'

const props = defineProps({
  user: Object,
  playlist: Object,
  playlistTracks: Object
})

const selectedTracks = ref([]);
const previewUrl = ref('');
const volume = ref(20);
const isPlaying = ref(false);
let wavesurfer = ref(null);

const isTrackSelected = (track) => {
    return selectedTracks.value.findIndex(t => t.id === track.id) !== -1;
}

const playPreview = (preview_url) => {
  if (preview_url) {
    previewUrl.value = preview_url;
  }
}

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
</script>

<template>
  <Header :user="props.user">
    <template #content>
        <div class="d-flex justify-content-center">
            <div>
                <h1>Playlists</h1>
                <div>
                    <div class="card" style="margin: 1rem; padding: 1rem">
                        <div class="row h-100">
                          <div class="col-3 d-flex align-items-center">
                            <img v-if="playlist['images'] !== null"  :src="playlist['images'][0]['url']" height="85" width="85"/>
                          </div>
                          <div class="col-9 d-flex flex-column justify-content-center">
                            <p>{{ playlist.name }}</p>
                            <p>{{ playlist.description }}</p>
                            <p>{{ playlist.tracks.total }} tracks</p>
                            <p>{{ playlist.public}}</p>
                          </div>
                        </div>
                      </div>
                </div>
                <div class="card" v-for="(result, index) in playlistTracks" :key="index" style="margin: 1rem; padding: 1rem">
                    <div class="row h-100">
                      <div class="col-3 d-flex align-items-center">
                        <input class="form-check-input me-3 ms-3" type="checkbox" value="" @change="toggleTrack(result)" :checked="isTrackSelected(result)"
                        >
                        <img :src="result.album['images'][0]['url']" height="85" width="85"/>
                      </div>
                      <div class="col-9 d-flex flex-column justify-content-center">
                        <div>
                          <button v-if="result.preview_url" @click="playPreview(result.preview_url)"  class="btn btn-outline-dark ">See Preview</button>
                          <button v-else disabled  class="btn btn-outline-dark">No Preview</button>
                        </div>
                        <div>
                          Artist: {{ result.artists[0]['name'] }}
                        </div>
                        <div>
                          Track: {{ result.name }}
                        </div>
                      </div>
                    </div>
                    <div v-if="previewUrl !== '' && previewUrl === result.preview_url" class="mt-3" style="background-color: #D5CFE1; padding: 1rem; border-radius: 1rem;">
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
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
  </Header>
</template>