<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';

const props = defineProps({
  user: Object
})

</script>

<template>
  <Header :user="props.user">
    <template #content>
        <div class="row h-100">
            <div class="container mt-4 mb-4 p-3 d-flex justify-content-center"> 
                <div class="card p-4" style="height: 50%">
                    <div class="d-flex flex-column justify-content-between align-items-center h-100">
                        <div class="image d-flex flex-column justify-content-center align-items-center">
                            <img :src="props.user.spotify_image" height="100" width="100" class="bg-info rounded-circle"/>
                            <span class="name mt-3" style="color: #f7f9fb">{{ props.user.name }}</span> 
                            <span class="idd">Type: {{ props.user.spotify_product }}</span> 
                            <div class="d-flex flex-row justify-content-center align-items-center gap-2"> 
                                <span class="idd1">{{ props.user.spotify_id }}</span> 
                                <span><i class="fa fa-copy"></i></span> 
                            </div> <div class="d-flex flex-row justify-content-center align-items-center mt-3"> 
                                <span class="number" style="color:  #f7f9fb;"> {{ props.user.spotify_followers }} 
                                    <span class="follow">Followers</span>
                                </span> 
                            </div> 
                        </div>
                        <div class=" px-2 rounded mt-4 date "> 
                            <span class="join" style="color:  #f7f9fb;">Joined {{ new Date(props.user.created_at).toISOString().substring(0, 10) }}</span> 
                        </div> 
                    </div> 
                </div>
            </div>
        </div>
    </template>
  </Header>
</template>

<style scoped>
.card {
    width: 350px;
    background-color: rgb(96, 129, 150, 0.3);
    border: none;
    transition: all 0.5s;
}

.image img {
    transition: all 0.5s
}

.card:hover .image img {
    transform: scale(1.1)
}

.name {
    font-size: 22px;
    font-weight: bold
}

.idd {
    font-size: 14px;
    font-weight: 600
}

.idd1 {
    font-size: 12px
}

.number {
    font-size: 22px;
    font-weight: bold
}

.follow {
    font-size: 12px;
    font-weight: 500;
    color: white
}

.text span {
    font-size: 13px;
    color: #545454;
    font-weight: 500
}

.join {
    font-size: 14px;
    color: #a0a0a0;
    font-weight: bold
}

.date {
    background-color: #5085A5
}
</style>