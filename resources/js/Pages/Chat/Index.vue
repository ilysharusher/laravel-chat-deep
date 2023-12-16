<script setup>

import {Head, Link, router, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {onBeforeMount, ref} from 'vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
    chats: {
        type: Array,
        default: () => [],
    },
});

const groupCheckbox = ref(false);
const groupList = ref([]);
const title = ref(null);

onBeforeMount(() => {
    window.Echo.channel(`store-message-status-event-${usePage().props.auth.user.id}-user`)
        .listen('.store-message-status-event', (e) => {
            props.chats.forEach(chat => {
                if (chat.id === e.chat_id) {
                    chat.unread_message_statuses_count = e.count;
                    chat.last_message = e.message;
                }
            });
        });
});

const store = (id) => {
    router.post(window.route('chats.store', {users: [id]}));
};

const storeGroup = () => {
    if (!groupList.value.length || groupList.value.length < 2) return;

    router.post(window.route('chats.store', {users: groupList.value, title: title.value}));

    groupCheckbox.value = false;
    groupList.value = [];
    title.value = null;
};

const toggleGroupCheckbox = () => {
    groupCheckbox.value = !groupCheckbox.value;

    if (!groupCheckbox.value) {
        groupList.value = [];
    }

};

const updateGroupList = (event, userId) => {
    if (event.target.checked) {
        groupList.value.push(userId);
    } else {
        groupList.value.splice(groupList.value.indexOf(userId), 1);
    }

    console.log(groupList.value);
};

const isUserInGroupList = (userId) => {
    return groupList.value.includes(userId);
};
</script>

<template>
    <Head title="Chats" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chats</h2>
        </template>

        <div class="py-12 flex text-center select-none">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4 text-gray-900 dark:text-gray-100"
                    >
                        <div class="p-5">Users</div>
                        <input
                            v-model="title"
                            type="text"
                            class="border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-300 rounded-lg shadow-sm m-3"
                            placeholder="Group title"
                            @keyup.enter="storeGroup"
                        >
                        <a
                            v-if="!groupList.length"
                            href="#"
                            class="block p-5 text-gray-900 dark:text-gray-100 hover:bg-blue-500 transition-colors duration-200"
                            :class="groupCheckbox ? 'bg-red-500' : 'bg-indigo-800'"
                            @click.prevent="toggleGroupCheckbox"
                        >{{ groupCheckbox ? 'Go back' : 'Make Group' }}
                        </a>
                        <div v-else class="flex">
                            <a
                                href="#"
                                class="w-1/2 block p-5 text-gray-900 dark:text-gray-100 bg-red-500 hover:bg-blue-500 transition-colors duration-200"
                                @click.prevent="toggleGroupCheckbox"
                            >Go back
                            </a>
                            <a
                                href="#"
                                class="w-1/2 block p-5 text-gray-900 dark:text-gray-100 bg-green-500 hover:bg-blue-500 transition-colors duration-200"
                                :class="groupList.length < 2 ? 'pointer-events-none opacity-50' : ''"
                                @click.prevent="storeGroup"
                            >Make Group
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="users">
                    <div
                        v-for="(user, id) in users"
                        :key="id"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                    >
                        <div class="p-5 text-gray-900 dark:text-gray-100">{{ user.id }} - {{ user.name }}</div>
                        <a
                            v-if="!groupCheckbox"
                            href="#"
                            class="block p-5 text-gray-900 dark:text-gray-100 bg-gray-500 hover:bg-blue-500 transition-colors duration-200"
                            @click.prevent="store(user.id)"
                        >Message
                        </a>
                        <div v-else>
                            <input
                                :id="'user-' + user.id"
                                type="checkbox"
                                class="hidden"
                                :value="user.id"
                                @change="updateGroupList($event, user.id)"
                            />
                            <label
                                :for="'user-' + user.id"
                                class="block p-5 text-gray-900 dark:text-gray-100 cursor-pointer hover:bg-blue-500 transition-colors duration-200"
                                :class="isUserInGroupList(user.id) ? 'bg-red-500' : 'bg-green-500'"
                            >{{ isUserInGroupList(user.id) ? 'Deselect' : 'Add to Group' }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="chats" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="(chat, id) in chats"
                    :key="id"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4"
                >
                    <div class="p-5 text-gray-900 dark:text-gray-100 border-b border-gray-600">
                        {{ chat.id }} - {{
                            chat.title ?? 'Chat'
                        }}
                    </div>
                    <div
                        v-if="chat.last_message"
                        class="mx-6 my-3"
                        :class="chat.unread_message_statuses_count ? 'text-red-500 font-bold' : 'text-gray-900 dark:text-gray-100 opacity-50'"
                    >
                        {{ chat.last_message.user_name }}:
                        {{ chat.last_message.text }}<br>
                        <span class="text-sm opacity-30">{{ chat.last_message.time }}</span>
                    </div>
                    <Link
                        :href="route('chats.show', chat.id)"
                        class="block p-5 text-gray-900 dark:text-gray-100 bg-gray-500 hover:bg-blue-500 transition-colors duration-200"
                    >
                        Open <span
                            v-if="chat.unread_message_statuses_count"
                            class="bg-red-500 text-white rounded-lg px-2 py-1 ml-0.5"
                        >{{ chat.unread_message_statuses_count }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
