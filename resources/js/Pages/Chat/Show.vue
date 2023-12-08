<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';

const props = defineProps({
    chat: {
        type: Object,
    },
    users: {
        type: Array,
        default: () => [],
    },
});

const interlocutors = props.users.map(
    (user) => user.id)
    .filter(
        (id) => id !== usePage().props.auth.user.id,
    );

const form = useForm({
    chat_id: props.chat.id,
    interlocutors: interlocutors,
    text: '',
});

const store = () => {
    window.axios.post(window.route('messages.store'), form)
        .then((result) => {
            console.log(result);
        });

    form.text = '';
};
</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chat</h2>
        </template>

        <div class="py-12 flex text-center">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="(user, id) in users"
                    :key="id"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <div class="p-5 text-gray-900 dark:text-gray-100">{{ user.id }} - {{ user.name }}</div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-5 text-gray-900 dark:text-gray-100">{{ props.chat.title ?? 'Your Chat' }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-5 text-gray-900 dark:text-gray-100">
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3 text-gray-900 dark:text-gray-100">
                        <input
                            v-model="form.text"
                            class="w-full border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-300 rounded-md shadow-sm"
                            type="text"
                        >
                    </div>
                    <a
                        class="block p-3 text-gray-900 dark:text-gray-100 bg-gray-500"
                        href="#"
                        @click.prevent="store"
                    >
                        Send message
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
