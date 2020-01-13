<template>
    <div class="new-reply">
        <div class="card" v-if="signedIn">
            <div class="card-body">
                <h5 class="card-title mb-4">Have something to say?</h5>
                <div class="form-group">
                    <wysiwyg name="content" v-model="content" :shouldClear="completed"></wysiwyg>
                </div>

                <button type="submit" class="btn btn-primary" @click="addReply">Post</button>
            </div>
        </div>

        <p v-else>
            Please <a href="/login">sign in</a> to participate in this thread.
        </p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            content: "",
            completed: false
        };
    },

    methods: {
        addReply() {
            axios
                .post(location.pathname + "/replies", { content: this.content })
                .catch(error => {
                    flash(error.response.data, "danger");
                })
                .then(({ data }) => {
                    this.content = "";
                    this.completed = true;

                    flash("Your reply has been posted.");

                    this.$emit("created", data);
                });
        }
    }
};
</script>