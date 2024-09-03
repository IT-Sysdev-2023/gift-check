<template>
    <a-select
        v-model:value="value"
        show-search
        placeholder="Select an item"
        :options="options"
        :filter-option="filterOption"
        @change="handleChange"
        @focus="onFocus"
    ></a-select>
</template>

<script lang="ts" setup>
import type { SelectProps } from "ant-design-vue";
import { ref, watch } from "vue";

const emit = defineEmits<{
    (e: "handleChange", id: string, acc: { [key: string]: any }): void;
    (e: "onFocus"): void;
}>();
const props = defineProps<{
    options: {
        value: number;
        label: string;
    }[];
    value?: string | undefined;
}>();
const options = ref<SelectProps["options"]>(props.options);
const handleChange = (value: string, acc: any) => {
    emit("handleChange", value, acc);
};
const onFocus = () => {
    emit("onFocus");
}
const filterOption = (input: string, option: any) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

const value = ref<string | undefined>(props.value);

</script>
