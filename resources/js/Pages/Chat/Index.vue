<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from '@inertiajs/vue3'

defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

const store = (id) => {
    router.post(route('chats.store', {users: [id]}));
};
</script>

<template>
    <Head title="Chats"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chats</h2>
        </template>

        <div class="py-12 flex">
            <div v-if="users" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="(user, id) in users"
                    :key="id"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">{{ user.id }} - {{ user.name }}</div>
                    <a
                        @click.prevent="store(user.id)"
                        href="#"
                        class="block p-6 text-gray-900 dark:text-gray-100 bg-gray-500">Message
                    </a>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Chat</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
