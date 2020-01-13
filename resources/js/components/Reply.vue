<template>
    <div :id="'reply-'+id" class="card reply" :class="isBest ? 'border-success': ''">
        <div class="card-header bg-transparent border-0 d-flex align-items-center">
            <div class="flex-grow-1">
                <img
                    :src="reply.author.avatar_path"
                    :alt="reply.author.name"
                    width="25"
                    height="25"
                    class="rounded mr-2"
                />

                <span>
                    <a :href="route('profile', reply.author.name)" v-text="reply.author.name"></a>
                </span>
            </div>

            <span class="text-muted font-size-sm" v-text="ago"></span>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <wysiwyg v-model="content"></wysiwyg>
                    </div>

                    <button class="btn btn-primary">Update</button>
                    <button class="btn btn-secondary ml-1" @click="resetForm" type="button">Cancel</button>
                </form>
            </div>

            <div v-else v-html="content"></div>
        </div>

        <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center text-muted font-size-sm">
            <div class="meta d-flex align-items-center">
                <favorite :reply="reply"></favorite>
                <span class="text-success" v-show="isBest">Best Reply</span>
            </div>

            <div class="actions" v-if="authorize('owns', reply) || (authorize('owns', reply.thread) && !this.isBest)">
                <div class="dropup">
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm text-nowrap"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        title="Actions"
                    >
                        <i class="fas fa-bars"></i>
                        <span class="d-lg-none">Actions</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <button
                            type="button"
                            class="dropdown-item"
                            @click="editing = true"
                            v-if="authorize('owns', reply)"
                        >
                            <i class="far fa-edit fa-fw"></i>
                            Edit
                        </button>
                        <button
                            type="button"
                            class="dropdown-item"
                            @click="destroy"
                            v-if="authorize('owns', reply)"
                        >
                            <i class="far fa-trash-alt fa-fw"></i>
                            Delete
                        </button>
                        <button
                            type="button"
                            class="dropdown-item"
                            @click="markBestReply"
                            v-if="authorize('owns', reply.thread) && !isBest"
                        >
                            <i class="far fa-check-circle fa-fw"></i>
                            Mark as best reply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Favorite from "./Favorite.vue";
import moment from "moment";

export default {
    props: ["reply"],

    components: { Favorite },

    data() {
        return {
            editing: false,
            id: this.reply.id,
            content: this.reply.content,
            isBest: this.reply.isBest
        };
    },

    computed: {
        ago() {
            return moment(this.reply.created_at).fromNow();
        }
    },

    created() {
        window.events.$on("best-reply-selected", reply => {
            this.isBest = reply.id === this.id;
        });
    },

    methods: {
        update() {
            let url = this.route("replies.update", [
                this.reply.thread.slug,
                this.id
            ]).url();

            axios
                .patch(url, {
                    content: this.content
                })
                .catch(error => {
                    flash(error.response.data, "danger");
                });

            this.editing = false;

            flash("Updated!");
        },

        destroy() {
            let url = this.route("replies.destroy", [
                this.reply.thread.slug,
                this.id
            ]).url();

            axios.delete(url);

            this.$emit("deleted", this.id);

            if (this.isBest) {
                window.events.$emit("best-reply-deleted");
            }

            flash("Deleted!", "danger");
        },

        resetForm() {
            this.content = this.reply.content;

            this.editing = false;
        },

        markBestReply() {
            let url = this.route("best-replies.store", [
                this.reply.thread.slug,
                this.id
            ]).url();

            axios.post(url);

            window.events.$emit("best-reply-selected", this.reply);
        }
    }
};
</script>