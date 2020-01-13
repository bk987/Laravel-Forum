<template>
    <div>
        <div class="d-flex align-items-center">
            <div class="avatar-upload rounded mr-2">
                <image-upload name="avatar" class="mr-1" @loaded="onLoad" v-if="canUpdate"></image-upload>
                <img :src="avatar" width="50" height="50" class="rounded" />
            </div>

            <h1 class="h4" v-text="user.name"></h1>
        </div>
    </div>
</template>

<script>
import ImageUpload from "./ImageUpload.vue";

export default {
    props: ["user"],

    components: { ImageUpload },

    data() {
        return {
            avatar: this.user.avatar_path
        };
    },

    computed: {
        canUpdate() {
            return this.authorize(user => user.id === this.user.id);
        }
    },

    methods: {
        onLoad(avatar) {
            this.avatar = avatar.src;

            this.persist(avatar.file);
        },

        persist(avatar) {
            let url = this.route("api.avatar.store", this.user.name).url();
            let data = new FormData();

            data.append("avatar", avatar);

            axios.post(url, data).then(() => {
                flash("Avatar uploaded!");
            });
        }
    }
};
</script>