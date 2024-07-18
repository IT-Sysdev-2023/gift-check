<template>
    <a-form-item :label="label">
        <a-input :value="value" readonly />
    </a-form-item>
</template>

<script>
export default {
    props: {
        label: String,
        value: String,
    },
};
</script>
