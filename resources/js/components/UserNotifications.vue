<template>
    <li class="nav-item dropdown" v-if="notifications.length">
        <a
            class="nav-link"
            href="#"
            id="notificationsDropdown"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Notifications
            <span class="badge badge-secondary" v-text="notifications.length"></span>
            <span class="sr-only">unread notifications</span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationsDropdown">
            <template v-for="(notification, index) in notifications">
                <a
                    class="dropdown-item"
                    :key="index"
                    :href="notification.data.link"
                    v-text="notification.data.message"
                    @click="markAsRead(notification)"
                ></a>
            </template>
        </div>
    </li>
</template>

<script>
export default {
    data() {
        return { notifications: false };
    },

    created() {
        axios
            .get(route("user.notifications", window.App.user.name).url())
            .then(response => (this.notifications = response.data));
    },

    methods: {
        markAsRead(notification) {
            axios.delete(
                route("user.notifications.destroy", [
                    window.App.user.name,
                    notification.id
                ]).url()
            );
        }
    }
};
</script>