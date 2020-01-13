<template>
    <div>
        <input id="trix" type="hidden" :name="name" :value="value" />

        <trix-editor ref="trix" input="trix" :placeholder="placeholder" class="trix-content"></trix-editor>
    </div>
</template>

<script>
import Trix from "trix";

export default {
    props: ["name", "value", "placeholder", "shouldClear"],

    mounted() {
        Trix.config.blockAttributes.heading1 = { tagName: "h4" };

        this.$refs.trix.addEventListener("trix-initialize", e => {
            const trix = e.target;
            const toolBar = trix.toolbarElement;

            toolBar
                .querySelector('[data-trix-button-group="file-tools"]')
                .remove();
            toolBar
                .querySelector('[data-trix-action="decreaseNestingLevel"]')
                .remove();
            toolBar
                .querySelector('[data-trix-action="increaseNestingLevel"]')
                .remove();
        });

        this.$refs.trix.addEventListener("trix-file-accept", e => {
            e.preventDefault();
        });

        this.$refs.trix.addEventListener("trix-change", e => {
            this.$emit("input", e.target.innerHTML);
        });

        this.$watch("shouldClear", () => {
            this.$refs.trix.value = "";
        });
    }
};
</script>