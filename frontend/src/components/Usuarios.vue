<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const usuarios = ref([]);

onMounted(async () => {
    const res = await axios.get('http://localhost:8000/api/usuarios');
    usuarios.value = res.data;
});

const nuevoUsuario = ref({
    nombre: '',
    email: '',
    password: '',
    rol: 'Colaborador',
    idSucursal: 1,
})

const crearUsuario = async () => {
    await axios.post('http://localhost:8000/api/usuarios', nuevoUsuario.value);
    const res = await axios.get('http://localhost:8000/api/usuarios');
    usuarios.value = res.data;
}

</script>

<template>
    <div class="p-6">
        <h1 class = "text-xl font-bold mb-4">Usuarios</h1>
        <form @submit.prevent="crearUsuario" class="space-y-2 mb-6">
            <input v-model="nuevoUsuario.nombre" placeholder="Nombre" class="border p-2 rounded w-full" />
            <input v-model="nuevoUsuario.email" placeholder="Email" class="border p-2 rounded w-full" />
            <input v-model="nuevoUsuario.password" type="password" placeholder="Contraseña" class="border p-2 rounded w-full" />
            <select v-model="nuevoUsuario.rol" class="border p-2 rounded w-full">
                <option>Administrador</option>
                <option>Colaborador</option>
            </select>
            <input v-model="nuevoUsuario.idSucursal" type="number" placeholder="ID Sucursal" class="border p-2 rounded w-full" />
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Crear</button>
        </form>
        <ul>
            <li v-for="u in usuarios" :key="u.id">
                {{ u.nombre }} - {{ u.email }} ({{ u.rol }})
            </li>
        </ul>
    </div>
</template>