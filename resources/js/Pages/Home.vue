<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import WaveSurfer from 'wavesurfer.js'

let wavesurfer = ref(null);
const query = ref('');
const searchResults = ref(null);
const searchType = ref('track');
const recommendType = ref('track');
const recommendations = ref(null);
const searchGenre = ref('');
const recommendationAmount = ref(10);

const genreOptions = [
  { value: 'acoustic', label: 'Acoustic' },
  { value: 'afrobeat', label: 'Afrobeat' },
  { value: 'alt-rock', label: 'Alt-Rock' },
  { value: 'alternative', label: 'Alternative' },
  { value: 'ambient', label: 'Ambient' },
  { value: 'anime', label: 'Anime' },
  { value: 'black-metal', label: 'Black Metal' },
  { value: 'bluegrass', label: 'Bluegrass' },
  { value: 'blues', label: 'Blues' },
  { value: 'bossanova', label: 'Bossa Nova' },
  { value: 'brazil', label: 'Brazil' },
  { value: 'breakbeat', label: 'Breakbeat' },
  { value: 'british', label: 'British' },
  { value: 'cantopop', label: 'Cantopop' },
  { value: 'chicago-house', label: 'Chicago House' },
  { value: 'children', label: 'Children' },
  { value: 'chill', label: 'Chill' },
  { value: 'classical', label: 'Classical' },
  { value: 'club', label: 'Club' },
  { value: 'comedy', label: 'Comedy' },
  { value: 'country', label: 'Country' },
  { value: 'dance', label: 'Dance' },
  { value: 'dancehall', label: 'Dancehall' },
  { value: 'death-metal', label: 'Death Metal' },
  { value: 'deep-house', label: 'Deep House' },
  { value: 'detroit-techno', label: 'Detroit Techno' },
  { value: 'disco', label: 'Disco' },
  { value: 'disney', label: 'Disney' },
  { value: 'drum-and-bass', label: 'Drum and Bass' },
  { value: 'dub', label: 'Dub' },
  { value: 'dubstep', label: 'Dubstep' },
  { value: 'edm', label: 'EDM' },
  { value: 'electro', label: 'Electro' },
  { value: 'electronic', label: 'Electronic' },
  { value: 'emo', label: 'Emo' },
  { value: 'folk', label: 'Folk' },
  { value: 'forro', label: 'Forro' },
  { value: 'french', label: 'French' },
  { value: 'funk', label: 'Funk' },
  { value: 'garage', label: 'Garage' },
  { value: 'german', label: 'German' },
  { value: 'gospel', label: 'Gospel' },
  { value: 'goth', label: 'Goth' },
  { value: 'grindcore', label: 'Grindcore' },
  { value: 'groove', label: 'Groove' },
  { value: 'grunge', label: 'Grunge' },
  { value: 'guitar', label: 'Guitar' },
  { value: 'happy', label: 'Happy' },
  { value: 'hard-rock', label: 'Hard Rock' },
  { value: 'hardcore', label: 'Hardcore' },
  { value: 'hardstyle', label: 'Hardstyle' },
  { value: 'heavy-metal', label: 'Heavy Metal' },
  { value: 'hip-hop', label: 'Hip-Hop' },
  { value: 'holidays', label: 'Holidays' },
  { value: 'honky-tonk', label: 'Honky Tonk' },
  { value: 'house', label: 'House' },
  { value: 'idm', label: 'IDM' },
  { value: 'indian', label: 'Indian' },
  { value: 'indie', label: 'Indie' },
  { value: 'indie-pop', label: 'Indie Pop' },
  { value: 'industrial', label: 'Industrial' },
  { value: 'iranian', label: 'Iranian' },
  { value: 'j-dance', label: 'J-Dance' },
  { value: 'j-idol', label: 'J-Idol' },
  { value: 'j-pop', label: 'J-Pop' },
  { value: 'j-rock', label: 'J-Rock' },
  { value: 'jazz', label: 'Jazz' },
  { value: 'k-pop', label: 'K-Pop' },
  { value: 'kids', label: 'Kids' },
  { value: 'latin', label: 'Latin' },
  { value: 'latino', label: 'Latino' },
  { value: 'malay', label: 'Malay' },
  { value: 'mandopop', label: 'Mandopop' },
  { value: 'metal', label: 'Metal' },
  { value: 'metal-misc', label: 'Metal Misc' },
  { value: 'metalcore', label: 'Metalcore' },
  { value: 'minimal-techno', label: 'Minimal Techno' },
  { value: 'movies', label: 'Movies' },
  { value: 'mpb', label: 'MPB' },
  { value: 'new-age', label: 'New Age' },
  { value: 'new-release', label: 'New Release' },
  { value: 'opera', label: 'Opera' },
  { value: 'pagode', label: 'Pagode' },
  { value: 'party', label: 'Party' },
  { value: 'philippines-opm', label: 'Philippines OPM' },
  { value: 'piano', label: 'Piano' },
  { value: 'pop', label: 'Pop' },
  { value: 'pop-film', label: 'Pop Film' },
  { value: 'post-dubstep', label: 'Post-Dubstep' },
  { value: 'power-pop', label: 'Power Pop' },
  { value: 'progressive-house', label: 'Progressive House' },
  { value: 'psych-rock', label: 'Psych Rock' },
  { value: 'punk', label: 'Punk' },
  { value: 'punk-rock', label: 'Punk Rock' },
  { value: 'r-n-b', label: 'R&B' },
  { value: 'rainy-day', label: 'Rainy Day' },
  { value: 'reggae', label: 'Reggae' },
  { value: 'reggaeton', label: 'Reggaeton' },
  { value: 'road-trip', label: 'Road Trip' },
  { value: 'rock', label: 'Rock' },
  { value: 'rock-n-roll', label: 'Rock & Roll' },
  { value: 'rockabilly', label: 'Rockabilly' },
  { value: 'romance', label: 'Romance' },
  { value: 'sad', label: 'Sad' },
  { value: 'salsa', label: 'Salsa' },
  { value: 'samba', label: 'Samba' },
  { value: 'sertanejo', label: 'Sertanejo' },
  { value: 'show-tunes', label: 'Show Tunes' },
  { value: 'singer-songwriter', label: 'Singer-Songwriter' },
  { value: 'ska', label: 'Ska' },
  { value: 'sleep', label: 'Sleep' },
  { value: 'songwriter', label: 'Songwriter' },
  { value: 'soul', label: 'Soul' },
  { value: 'soundtracks', label: 'Soundtracks' },
  { value: 'spanish', label: 'Spanish' },
  { value: 'study', label: 'Study' },
  { value: 'summer', label: 'Summer' },
  { value: 'swedish', label: 'Swedish' },
  { value: 'synth-pop', label: 'Synth-Pop' },
  { value: 'tango', label: 'Tango' },
  { value: 'techno', label: 'Techno' },
  { value: 'trance', label: 'Trance' },
  { value: 'trip-hop', label: 'Trip-Hop' },
  { value: 'turkish', label: 'Turkish' },
  { value: 'work-out', label: 'Work Out' },
  { value: 'world-music', label: 'World Music' },
];

const props = defineProps({
  user: Object
})

const selectedTracks = ref([]);
const selectedTracksForPlaylist = ref([]);
const volume = ref(20);

const playlistName = ref('');

const submitForm = async () => {
  try {

    const searchString = searchType.value + ':' + query.value;

    const response = await axios.get('/getSpotifyResults', {
        params: { 
            query: searchString,
        }
    });
    searchResults.value = response.data;
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

const searchAlike = async () => {
    const params = { 
        amount: recommendationAmount.value,
        seed_genres: searchGenre.value
    }
    if (recommendType.value === 'track') {
        params['seed_tracks'] = selectedTracks.value.map(track => track.id).join(',');
    } else if (recommendType.value === 'artist') {
        params['seed_artists'] = selectedTracks.value.map(track => track.artists[0].id).join(',');
    }
    try {
        const response = await axios.get('/getRecommendations', {
            params: params
        });
        recommendations.value = response.data;
        console.log(recommendations.value)
    } catch (error) {
        console.error('There was an error fetching the results:', error);
    }
}

const toggleTrack = (track) => {
  const index = selectedTracks.value.findIndex(t => t.id === track.id);
  if (index === -1) {
    selectedTracks.value.push(track);
  } else {
    selectedTracks.value.splice(index, 1);
  }
};

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
  <Header :user="props.user">
    <template #content>
      <div class="row h-100">
        <div class="col-6">
          <form @submit.prevent="submitForm" class="d-flex align-items-end justify-content-between">
            <div class="ms-3">
              <label for="query">Search Spotify:</label>
              <input type="text" v-model="query" id="query" required class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
            </div>
            <div class="d-flex align-items-end">
              <div class="ms-3">
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
              <span class="ms-3">by</span>
              <div class="ms-3">
                <select class="form-select simple-dropdown" aria-label="Default select example" v-model="searchType">
                  <option value="track" selected>Track</option>
                  <option value="artist">Artist</option>
                </select>
              </div>
            </div>
          </form>
          <div class="d-flex align-items-end justify-content-end mt-3">
            <div class="ms-3">
              <button type="submit" class="btn btn-primary" @click="searchAlike" :disabled="selectedTracks.length == 0">Recommend</button>
            </div>
            <span class="ms-3">by</span>
            <div class="ms-3">
              <select class="form-select simple-dropdown" aria-label="Default select example" v-model="recommendType">
                <option value="track" selected>Track</option>
                <option value="artist">Artist</option>
              </select>
            </div>
            <div class="ms-3">
              <select class="form-select simple-dropdown" aria-label="Default select example" v-model="searchGenre" :disabled="selectedTracks.length >= 5">
                <option value="" selected>Select a genre</option>
                <option v-for="genre in genreOptions" :key="genre.value" :value="genre.value" selected>{{ genre.label }}</option>
              </select>
            </div>
          </div>
          <div v-if="searchResults" style="overflow-y: scroll; max-height: 76vh; margin-top: 1rem">
            <div>
              <div class="card" v-for="(result, index) in searchResults['tracks']['items']" :key="index" style="margin: 1rem; padding: 1rem">
                <div class="row h-100">
                  <div class="col-3 d-flex align-items-center">
                    <input class="form-check-input me-3 ms-3" type="checkbox" value="" @change="toggleTrack(result)" :disabled="isSelectionFull() && !isTrackSelected(result)" :checked="isTrackSelected(result)"
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
                    <!-- <label for="customRange1" class="form-label">Volume</label>
                    <input type="range" class="form-range" id="customRange1" v-model="volume"> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="vr" style="position: absolute; height: 90vh;"></div>
            <div>
              <div class="d-flex align-items-end">
                <div class="ms-3">
                  <span>Playlist Name</span>
                  <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" v-model="playlistName">
                </div>
                <div class="ms-3">
                  <button 
                    @click="createPlaylist()" 
                    class="btn btn-primary"
                    :disabled="playlistName == '' || recommendations == null"
                  >
                    Create Playlist
                  </button>
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