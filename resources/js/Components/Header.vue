<script setup>
const props = defineProps({
    user: Object
})

const logout = () => {
  window.location = '/logout';
}

const goToPlaylists = () => {
  window.location = '/playlists';
}

const goToHome = () => {
  window.location = '/';
}
</script>
<template>
    <header style="height: 8vh">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div>
                    <span style="font-size: 1.5rem; cursor: pointer" @click="goToHome()">SpotifyMe</span>
                </div>
                <div class="ms-3">
                    <img :src="props.user.spotify_image" alt="" height="45" class="bg-info rounded-circle">
                    <span class="ms-3">{{ props.user.name }}</span>
                    <span class="ms-3">Type: {{ props.user.spotify_product }}</span>
                    <span class="ms-3">Followers: {{ props.user.spotify_followers }}</span>
                    <button @click="goToPlaylists()" class="btn btn-danger ms-3">Playlists</button>
                </div>
            </div>
            <button @click="logout()" class="btn btn-danger">Logout</button>
        </div>
    </header>

    <div class="main_content">
        <slot name="content" />
    </div>
</template>

<style scoped>
/* Add your header styles here */
header {
background-color: #333;
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
height: 92vh;
}
</style>