<script>
import Replies from "../components/Replies.vue";
import BestReply from "../components/BestReply.vue";

export default {
    props: ["thread"],

    components: { Replies, BestReply },

    data() {
        return {
            locked: this.thread.locked,
            subscribed: this.thread.isSubscribedTo,
            title: this.thread.title,
            content: this.thread.content,
            form: {},
            editing: false,
            bestReply: this.thread.bestReply
        };
    },

    computed: {
        lockHtml() {
            return this.locked ?
                '<i class="fas fa-unlock fa-fw"></i> Unlock' :
                '<i class="fas fa-lock fa-fw"></i> Lock';
        },

        subscribeHtml() {
            return this.subscribed ?
                '<i class="fas fa-star fa-fw"></i> Unsubscribe' :
                '<i class="far fa-star fa-fw"></i> Subscribe';
        }
    },

    created() {
        this.resetForm();

        window.events.$on("best-reply-selected", reply => {
            this.bestReply = reply;
        });

        window.events.$on("best-reply-deleted", () => {
            this.bestReply = null;
        });
    },

    methods: {
        toggleLock() {
            let url = this.route('locked-threads.store', this.thread.slug).url();
            
            axios[this.locked ? "delete" : "post"](url);

            this.locked = !this.locked;
        },

        toggleSubscription() {
            let url = this.route('thread-subscriptions.store', this.thread.slug).url();

            axios[this.subscribed ? "delete" : "post"](url);

            this.subscribed = !this.subscribed;
        },

        update() {
            let url = this.route('threads.show', this.thread.slug).url();

            axios.patch(url, this.form).then(() => {
                this.editing = false;
                this.title = this.form.title;
                this.content = this.form.content;

                flash("Your thread has been updated.");
            });
        },

        destroy() {
            let url = this.route('threads.destroy', this.thread.slug).url();

            axios.delete(url).then(() => {
                flash("Your thread has been deleted. Redirecting...");
                window.location.href = this.route('threads.index').url();
            });
        },

        resetForm() {
            this.form = {
                title: this.thread.title,
                content: this.thread.content
            };

            this.editing = false;
        }
    }
};
</script>