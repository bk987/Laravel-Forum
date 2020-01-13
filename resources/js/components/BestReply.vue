<template>
    <div class="card best-reply border-success">
        <div class="card-header bg-transparent border-0 d-flex align-items-center">
            <div class="flex-grow-1">
                <img
                    :src="reply.author.avatar_path"
                    :alt="reply.author.name"
                    width="25"
                    height="25"
                    class="mr-2"
                />

                <span>
                    <a :href="route('profile', reply.author.name)" v-text="reply.author.name"></a>
                </span>
            </div>

            <span class="text-success">Best Reply</span>
        </div>

        <div class="card-body">
            <div v-html="reply.content"></div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["reply"]
};
</script>