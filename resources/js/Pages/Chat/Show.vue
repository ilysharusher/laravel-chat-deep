<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {onBeforeMount, ref} from 'vue';

const props = defineProps({
    chat: {
        type: Object,
    },
    users: {
        type: Array,
        default: () => [],
    },
    messages: {
        type: Array,
        default: () => [],
    },
    isLastPage: {
        type: Boolean,
        default: false,
    },
});

const messages = ref(props.messages.slice().reverse());
const userId = usePage().props.auth.user.id;
let page = 1;
const isLastPage = ref(props.isLastPage);

onBeforeMount(() => {
    window.Echo.channel(`store-message-event-${props.chat.id}-chat`)
        .listen('.store-message-event', (e) => {
            messages.value.push(e.message);

            if (window.location.href === window.route('chats.show', {chat: props.chat.id})) {
                window.axios.patch(window.route('update.message.status'), {
                    user_id: userId,
                    message_id: e.message.id,
                });
            }
        });
});

const interlocutors = props.users.map(
    (user) => user.id)
    .filter(
        (id) => id !== userId,
    );

const form = useForm({
    chat_id: props.chat.id,
    interlocutors: interlocutors,
    text: '',
});

const store = () => {
    window.axios.post(window.route('messages.store'), form)
        .then((result) => {
            messages.value.push(result.data);
        });

    form.text = '';
};

const loadMoreMessages = () => {
    window.axios.post(window.route('load.messages', {chat_id: props.chat.id, page: ++page}))
        .then((result) => {
            messages.value = result.data.messages.concat(messages.value);
            isLastPage.value = result.data.is_last_page;
        });
};
</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chat</h2>
        </template>

        <div class="py-12 flex text-center select-none">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="(user, id) in users"
                    :key="id"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <div
                        class="p-5 text-gray-900 dark:text-gray-100"
                        :class="user.id === userId ? 'bg-blue-500' : ''"
                    >
                        {{ user.name }}
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-4 text-gray-900 dark:text-gray-100">{{ props.chat.title }}</div>
                </div>
                <div
                    v-if="messages.length"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <a
                        v-if="!isLastPage"
                        class="block text-gray-900 dark:text-gray-100 border-b border-gray-600 py-3 hover:bg-blue-500 transition-colors duration-200"
                        href="#"
                        @click.prevent="loadMoreMessages"
                    >
                        Load more
                    </a>
                    <div
                        class="p-5 text-gray-900 dark:text-gray-100 text-start"
                    >
                        <div
                            v-for="(message, id) in messages"
                            :key="id"
                            class="mb-4"
                            :class="message.is_owner ? 'text-right' : 'text-left'"
                        >
                            <div
                                class="p-3 inline-block"
                                :class="
                                    message.is_owner
                                        ? 'text-gray-900 dark:text-gray-100 bg-gray-700'
                                        : 'text-gray-900 dark:text-gray-100 bg-gray-900'
                                "
                            >
                                <p>{{ message.user_name }}</p>
                                <p class="font-bold">{{ message.text }}</p>
                                <p class="italic text-xs">{{ message.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3 text-gray-900 dark:text-gray-100">
                        <input
                            v-model="form.text"
                            class="w-full border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-300 rounded-lg shadow-sm"
                            type="text"
                            @keydown.enter.prevent="store"
                        >
                    </div>
                    <a
                        class="block p-3 text-gray-900 dark:text-gray-100 bg-gray-500 hover:bg-blue-500 transition-colors duration-200"
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
