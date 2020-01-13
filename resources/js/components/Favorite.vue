<template>
    <button :class="buttonClasses" @click="toggle" title="Favorite">
        <i :class="iconClasses"></i>
        <span v-text="count"></span>
    </button>
</template>

<script>
export default {
    props: ["reply"],

    data() {
        return {
            count: this.reply.favoritesCount,
            active: this.reply.isFavorited
        };
    },

    computed: {
        buttonClasses() {
            return ["btn btn-sm btn-light", this.active ? "text-primary" : ""];
        },

        iconClasses() {
            return ["mr-1 fa-heart", this.active ? "fas" : "far"];
        }
    },

    methods: {
        toggle() {
            this.active ? this.destroy() : this.create();
        },

        create() {
            axios.post(
                this.route("favorites.store", [
                    this.reply.thread.slug,
                    this.reply.id
                ]).url()
            );

            this.active = true;
            this.count++;
        },

        destroy() {
            axios.delete(
                this.route("favorites.destroy", [
                    this.reply.thread.slug,
                    this.reply.id
                ]).url()
            );

            this.active = false;
            this.count--;
        }
    }
};
</script>