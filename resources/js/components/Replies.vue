<template>
    <div>
        <div class="replies">
            <template v-for="(reply, index) in items">
                <reply :reply="reply" @deleted="remove(index)" :key="reply.id"></reply>
            </template>
        </div>

        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <p class="mt-5" v-if="$parent.locked">This thread has been locked. No more replies are allowed.</p>

        <new-reply @created="addToCollection" v-else></new-reply>
    </div>
</template>

<script>
import Reply from "./Reply.vue";
import NewReply from "./NewReply.vue";
import collection from "../mixins/collection";

export default {
    components: { Reply, NewReply },

    mixins: [collection],

    data() {
        return { dataSet: false };
    },

    created() {
        this.fetch();
    },

    methods: {
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },

        url(page) {
            if (!page) {
                let query = location.search.match(/page=(\d+)/);

                page = query ? query[1] : 1;
            }

            return `${location.pathname}/replies?page=${page}`;
        },

        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;

            window.scrollTo(0, 0);
        },

        addToCollection(item) {
            if (this.dataSet.current_page < this.dataSet.last_page) {
                return;
            }

            this.add(item);
        }
    }
};
</script>
