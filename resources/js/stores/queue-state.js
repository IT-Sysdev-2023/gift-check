import { defineStore } from "pinia";
import { ref } from "vue";

export const useQueueState = defineStore("useProgressStore", () => {
    const isGenerateVisible = ref(false);
    const isFloatButtonVisible = ref(false);
    const isFloatOpen = ref(false);

    function setGenerateButton(value) {
        isGenerateVisible.value = value;
    }

    function setFloatButton(value) {
        isFloatButtonVisible.value = value;
    }

    function setOpenFloat(value) {
        isFloatOpen.value = value;
    }

    return {
        isFloatOpen,
        isGenerateVisible,
        isFloatButtonVisible,
        setGenerateButton,
        setFloatButton,
        setOpenFloat,
    };
});
