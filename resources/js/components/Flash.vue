<template>
    <div
        class="alert alert-flash"
        :class="'alert-'+level"
        role="alert"
        v-show="show"
        v-text="content"
    ></div>
</template>

<script>
export default {
    props: ["message"],

    data() {
        return {
            content: this.message,
            level: "success",
            show: false
        };
    },

    created() {
        if (this.message) {
            this.flash();
        }

        window.events.$on("flash", data => this.flash(data));
    },

    methods: {
        flash(data) {
            if (data) {
                this.content = data.message;
                this.level = data.level;
            }

            this.show = true;

            this.hide();
        },

        hide() {
            setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }
};
</script>