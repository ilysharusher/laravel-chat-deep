<script setup>

import {Head, Link, router} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    users: {
        type: Array,
        default: () => [],
    },
    chats: {
        type: Array,
        default: () => [],
    },
});

const store = (id) => {
    router.post(window.route('chats.store', {users: [id]}));
};
</script>

<template>
    <Head title="Chats" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chats</h2>
        </template>

        <div class="py-12 flex text-center">
            <div v-if="users" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="(user, id) in users"
                    :key="id"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <div class="p-5 text-gray-900 dark:text-gray-100">{{ user.id }} - {{ user.name }}</div>
                    <a
                        href="#"
                        class="block p-5 text-gray-900 dark:text-gray-100 bg-gray-500"
                        @click.prevent="store(user.id)"
                    >Message
                    </a>
                </div>
            </div>
            <div v-if="chats" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="(chat, id) in chats"
                    :key="id"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <div class="p-5 text-gray-900 dark:text-gray-100">{{ chat.id }} - {{ chat.title ?? 'Chat' }}</div>
                    <Link
                        :href="route('chats.show', chat.id)"
                        class="block p-5 text-gray-900 dark:text-gray-100 bg-gray-500"
                    >
                        Open
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
