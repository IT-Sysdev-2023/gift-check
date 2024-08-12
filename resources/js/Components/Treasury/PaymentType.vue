<template>
    <a-space v-if="type === '2'">
        <a-form-item label="Bank Name:" name="bank">
            <a-input v-model:value="form.bankName" />
        </a-form-item>
        <a-form-item label="Account Number:" name="acc">
            <a-input v-model:value="form.accountNumber" />
        </a-form-item>
        <a-form-item label="Check Number:" name="check">
            <a-input v-model:value="form.checkNumber" />
        </a-form-item>
        <a-form-item label="Check Amount:" name="check">
            <a-input v-model:value="form.checkAmount" />
        </a-form-item>
    </a-space>
    <a-form-item label="Cash Amount:" name="amount" v-else>
        <a-input v-model:value="form.amount" />
    </a-form-item>
</template>

<script setup lang="ts">
defineProps<{
    type: string;
    form: any;
}>();
</script>

<style lang="scss" scoped></style>
