<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import WaveSurfer from 'wavesurfer.js'
import SongList from '@/Components/SongList.vue';


const props = defineProps({
  user: Object,
  playlist: Object,
  playlistTracks: Object
})

const referenceSongs = ref([]);
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

const updateReferenceSongs = (songs) => {
  referenceSongs.value = songs;
}
</script>

<template>
  <Header :user="props.user" @update:referenceSongs="updateReferenceSongs">
    <template #content>
        <div class="d-flex justify-content-center row h-100" style="padding: 0rem 20vw 0rem 20vw">
            <div style="background:linear-gradient(180deg, #897351, #575b58 );border-radius: 22px; padding-bottom: 1rem; padding-right: 0rem;  height: 80vh; padding-left: 0rem; box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19); display: flex; flex-direction: column;">
                <div>
                    <div style="padding: 1rem; width: 100%; ; border-radius: 22px 22px 0px 0px">
                        <div class="row h-100">
                          <div class="col-3 d-flex align-items-center justify-content-center">
                            <img v-if="playlist['images'] !== null"  :src="playlist['images'][0]['url']" height="150" width="150"/>
                          </div>
                          <div class="col-9 d-flex flex-column justify-content-between">
                            <div class="d-flex flex-column">
                              <div class="d-flex justify-content-between">
                                <span style="color: #f7f9fb;  font-weight: 500; font-size: 26px">{{ playlist.name }}</span>
                                <a :href="playlist.external_urls['spotify']" target="_blank" style="text-decoration: none; color: white;">
                                    See on
                                  <img class="mx-2" src="../../../icons/Spotify_Logo_RGB_White.png" height="34" ></img>
                                </a>
                              </div>
                              <span style="color: #f7f9fb;  font-weight: 500;">{{ playlist.description }}</span>
                            </div>
                            <div>
                              <span style="color: #f7f9fb;  font-weight: 500;">{{ playlist.tracks.total }} Tracks </span>
                            </div>
                          </div>
                        </div>
                      </div>
                </div>
                <div style="padding-right: 1rem; padding-left: 1rem; display: flex; flex-direction: column; flex: 1; min-height: 0px;">
                  <SongList
                    :isSearchingSongs="false"
                    :songs="playlistTracks"
                    v-model:referenceSongs="referenceSongs"
                    :searchGenre="searchGenre"
                    isPlaylist
                  >
                  </SongList>
                  <div v-if="playlistTracks" class="me-3 mt-3 d-flex justify-content-end">
                    <span style="color: #f7f9fb; font-weight: 600">
                     Showing {{ playlistTracks.length }} results
                    </span>
                  </div>
                </div>
            </div>
        </div>
    </template>
  </Header>
</template>