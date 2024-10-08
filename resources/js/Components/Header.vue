<script setup>
import { ref, onMounted, watch, defineEmits } from 'vue';
import PrimaryButton from './Buttons/PrimaryButton.vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    isHome: Boolean
})

const emit = defineEmits(['update:referenceSongs']);

const imageSrc = ref('../../icons/caret-down.svg');

const recommendations = defineModel('recommendations');
const referenceSongs = ref([]);
const isSearching = defineModel('isSearching');

const changeImage = (newSrc)  => {
    imageSrc.value = newSrc;
}

const goToHome = () => {
  window.location = '/';
}
const triggerToast = () => {
    const toast = document.getElementById('successPlaylist')
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast)
    toastBootstrap.show()    
}

defineExpose({
    triggerToast
})

const fetchFromLocalStorage = () => {
    if (localStorage.getItem('recc')) {
        referenceSongs.value = JSON.parse(localStorage.getItem('recc'));
    }

    emit('update:referenceSongs', referenceSongs.value);
}

onMounted(() => {
    fetchFromLocalStorage();
})

const searchGenre = ref('');
const recommendType = ref('track');
const recommendationAmount = ref(10);

const searchAlike = async () => {
    const params = { 
        amount: recommendationAmount.value,
        seed_genres: searchGenre.value
    }
    if (recommendType.value === 'track') {
        params['seed_tracks'] = referenceSongs.value.map(track => track.id).join(',');
    } else if (recommendType.value === 'artist') {
        params['seed_artists'] = referenceSongs.value.map(track => track.artists[0].id).join(',');
    }

    isSearching.value = true;

    try {
        const response = await axios.get('/getRecommendations', {
            params: params
        });

        if (response.status === 200) {
            const toastLiveExample = document.getElementById('successRecommendations')
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
            toastBootstrap.show()
        }

        recommendations.value = response.data;
    } catch (error) {
        console.error('There was an error fetching the results:', error);
    }

    isSearching.value = false;
}

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

const removeReference = (referenceIndex) => {
    referenceSongs.value.splice(referenceIndex, 1);
    localStorage.setItem('recc', JSON.stringify(referenceSongs.value));
}
</script>

<template>
    <header style="height: 8vh;" class="d-flex flex-column justify-content-center">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <!-- Brand -->
                <span class="navbar-brand" style="font-size: 1.5rem; cursor: pointer; color: #292D2A;" @click="goToHome()">TrackyMe</span>
    
                <!-- Regular Navbar for Larger Screens -->
                <div class="collapse navbar-collapse d-none d-lg-block" id="navbarNavDesktop">
                    <ul class="navbar-nav me-auto d-flex justify-content-between">
                        <div>
                            <li class="nav-item">
                                <a class="nav-link active" href="/recommender" style="color: #292D2A;">Recommender</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="/playlists" style="color: #292D2A;">Playlists</a>
                            </li>
                        </div>
                        <li class="nav-item dropdown">
                            <button 
                                class="btn btn-light action-button profile-dropdown" 
                                type="button" id="dropdownMenuButton2" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false" 
                                style="background: transparent; color: #292D2A; border-color: #292D2A"
                                @mouseenter="changeImage('../../icons/caret-down_white.svg.svg')" 
                                @mouseleave="changeImage('../../icons/caret-down.svg.svg')" 
                            >
                            <span style="font-weight: 600" >
                                {{ props.user.name }}
                            </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2" style="padding: 6px 0px 0px 0px">
                            <li style="padding: 0px 6px 6px 6px;"><a class="dropdown-item" href="/profile" style="border-radius: 12px">Profile</a></li>
                                <li style="padding: 0px 6px 6px 6px;">
                                    <a class="dropdown-item danger" href="/logout" style="border-radius: 12px;">
                                        Logout
                                        <img src="../../icons/box-arrow-right.svg" style="margin-left: 4px"></img>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
    
                <!-- Hamburger Button for Mobile -->
                <button 
                    class="navbar-toggler d-block d-lg-none" 
                    type="button" 
                    data-bs-toggle="offcanvas" 
                    data-bs-target="#offcanvasNavbar" 
                    aria-controls="offcanvasNavbar"
                    style="border: none; color: #292D2A;"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
    
                <!-- Offcanvas Menu for Mobile -->
                <div class="offcanvas offcanvas-end d-block d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                            <li class="nav-item">
                                <a class="nav-link active" href="/recommender" style="color: #292D2A;">Recommender</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="/playlists" style="color: #292D2A;">Playlists</a>
                            </li>
                            <a 
                            class="nav-link dropdown-toggle" 
                            href="#" 
                            id="navbarDropdown" 
                            role="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            style="color: #292D2A; font-weight: 600;"
                        >
                            {{ props.user.name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/profile">Profile</a></li>
                            <li>
                                <a class="dropdown-item danger" href="/logout">
                                    Logout
                                    <img src="../../icons/box-arrow-right.svg" style="margin-left: 4px;"></img>
                                </a>
                            </li>
                        </ul>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    
        <!-- Toasts (keep outside nav to avoid collapse issues) -->
        <div class="toast-container position-fixed bottom-0 end-1 p-3">
            <div id="successRecommendations" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-center justify-content-between" style="background-color: #34897A;"> 
                    Success! Check your recommendations.
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            <div id="successPlaylist" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-center justify-content-between" style="background-color: #34897A;"> 
                    Success Creating Playlist!
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </header>

    <div class="main_content">
        <slot name="content" />        
    </div>

    <div class="floating-button">
        <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 0.3rem; left: 0.4rem" v-if="referenceSongs != undefined && referenceSongs.length > 0">
            {{ referenceSongs.length }}
            <span class="visually-hidden">unread messages</span>
        </span>
        <button 
            type="submit"
            class="btn" 
            data-bs-toggle="modal" data-bs-target="#exampleModal"
            style="background-color: #34897A; color: #f7f9fb; padding: 13px; border-radius: 50%;"
        >
            <img src="../../icons/file-earmark-music.svg" height="30" width="30"></img>
        </button>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #f7f9fb; border-radius: 12px">
                <div class="modal-header" style="background-color:#34897A; color: white; border-radius: 12px 12px 26px 26px;">
                    <h5 class="modal-title" id="exampleModalLabel">Recommendations Builder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 64vh; overflow-y: auto;">
                    <div class="d-flex justify-content-center">
                        <span style="font-size: 1.2rem; font-weight: 500; color: #27252a" class="label-required">
                            Tracks
                        </span>
                    </div>
                    <div class="card" style="height: 30vh; margin-left: 2rem; margin-right: 2rem; margin-top: 0.4rem; margin-bottom: 0.4rem; background-color: #646865; overflow-y: scroll; padding-bottom: 0.7rem">
                        <div v-if="referenceSongs.length > 0">
                            <div 
                                class="card"
                                style="margin: 0.7rem 0.7rem 0rem 0.7rem; background-color: #f7f9fb; color: white; border: none;" 
                                v-for="(reference, index) in referenceSongs" :key="reference.id"
                            >
                                <div class="card-body d-flex justify-content-between" style="padding: 1rem; color: #292D2A; font-weight: 500">
                                    <div class="d-flex align-items-center" style="width: 90%">
                                        {{ reference.name }} - {{ reference.artists[0]['name'] }}
                                    </div>
                                    <div @click="removeReference(index)" style="cursor: pointer" class="d-flex align-items-center">
                                        <img class="toggle-button"  src="../../icons/trash.svg" height="24" width="24" style="cursor: pointer"></img>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="h-100 d-flex align-items-center justify-content-center">
                            <p style="font-size: 20px; color: #f7f9fb; font-weight: 500">Try adding any track</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <span style="font-size: 1.2rem; font-weight: 500; color: #27252a">
                            Genre
                        </span>
                    </div>
                    <div class="d-flex justify-content-center" style="margin-top: 0.4rem">
                        <select class="form-select" style="width: 100%; margin-left: 2rem; margin-right: 2rem; background-color: #646865; color: white;" aria-label="Default select example" v-model="searchGenre">
                            <option value="" selected>Select a genre</option>
                            <option v-for="genre in genreOptions" :key="genre.value" :value="genre.value" selected>{{ genre.label }}</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-center" style="margin-top: 0.4rem">
                        <span style="font-size: 1.2rem; font-weight: 500; color: #27252a">
                            Recommend By
                        </span>
                    </div>
                    <div class="d-flex justify-content-center" style="margin-top: 0.4rem">
                        <select class="form-select" style="width: 100%; margin-left: 2rem; margin-right: 2rem; background-color: #646865; color: white;" aria-label="Default select example" v-model="recommendType">
                            <option value="track" selected>Track</option>
                            <option value="artist">Artist</option>
                        </select>
                    </div>
                    <div style="margin-top: 0.4rem" class="d-flex justify-content-center">
                        <div style="width: 100%; margin-left: 2rem; margin-right: 2rem" class="d-flex align-items-center flex-column">
                            <span style="font-size: 1.2rem; font-weight: 500; color: #27252a" class="label-required">
                                Amount
                            </span>
                            <div style="width: 100%" class="d-flex">
                                <span class="badge bg-secondary me-3" style="min-width: 2rem">{{ recommendationAmount }}</span>
                                <input type="range" class="form-range" id="customRange1" min="1" max="50" v-model="recommendationAmount">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color:#34897A">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <PrimaryButton
                        v-if="isHome"
                        label="Get Recommendations"
                        class="rec-button"
                        type="submit"
                        @click="searchAlike"
                        :loading="isSearching"
                        :disabled="referenceSongs.length == 0"
                        data-bs-dismiss="modal"
                        style="border: none !important;"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add your header styles here */

@media (max-width: 970px) {
    .main_content {
        padding: 0rem 0 0rem 0 !important;
        flex: 1 1 auto; /* Content will take the remaining space */
        height: 92vh;
    }
}

header {
    flex: 0 0 auto;
    color: #fff;
    padding: 1rem;
}
nav ul {
    list-style-type: none;
    padding: 0;
}
nav ul li {
    display: inline-block;
    margin-right: 1rem;
}
nav ul li a {
    color: #fff;
    text-decoration: none;
}

.main_content {
    padding: 1rem;
    flex: 1 1 auto; /* Content will take the remaining space */
    height: 92vh;
}

.floating-button {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    border-radius: 50%;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}

.profile-dropdown:hover {
    background-color: #292D2A !important;
    transition: 0.5s;
}

.profile-dropdown:hover span {
    color: white !important;	
    transition: 0.5s;
}

.label-required::after {
    content: ' * ';
    color: red;
  }

  @media (max-width: 991px) { /* Bootstrap 'lg' breakpoint and below */
  .navbar-collapse.d-none {
      display: none !important;
  }
  .navbar-toggler.d-block {
      display: block !important;
  }
  .offcanvas.d-block {
      display: block !important;
  }
}

/* Hide the offcanvas button and show the desktop navbar on larger screens */
@media (min-width: 992px) { /* Bootstrap 'lg' breakpoint and above */
  .navbar-collapse.d-none {
      display: block !important;
  }
  .navbar-toggler.d-block {
      display: none !important;
  }
  .offcanvas.d-block {
      display: none !important;
  }
}
  
</style>

<style css> 

.rec-button > .btn {
    border: none !important;
}
</style>