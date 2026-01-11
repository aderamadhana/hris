<script setup>
import axios from 'axios';
import L from 'leaflet';
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: Object,
});

const emit = defineEmits(['update:modelValue']);

const mapRef = ref(null);
let map, marker;

onMounted(() => {
    map = L.map(mapRef.value).setView([-6.2, 106.816666], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    marker = L.marker([-6.2, 106.816666], { draggable: true }).addTo(map);

    marker.on('dragend', updateLocation);
    map.on('click', (e) => {
        marker.setLatLng(e.latlng);
        updateLocation();
    });
});

async function updateLocation() {
    const { lat, lng } = marker.getLatLng();

    // reverse geocoding GRATIS
    const res = await axios.get(`https://nominatim.openstreetmap.org/reverse`, {
        params: {
            format: 'json',
            lat,
            lon: lng,
        },
    });

    emit('update:modelValue', {
        ...props.modelValue,
        latitude: lat,
        longitude: lng,
        alamat_penempatan: res.data.display_name,
    });
}
</script>

<template>
    <div>
        <div ref="mapRef" style="height: 300px; border-radius: 12px"></div>
    </div>
</template>
