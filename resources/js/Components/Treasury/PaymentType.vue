<template>
    <a-form :label-col="{ span: 7 }" :wrapper-col="{ span: 15 }">
        <div v-if="form.paymentType.type === '2'">
            <a-form-item label="Bank Name:" name="bank" :validate-status="form.errors['paymentType.bankName'] ? 'error' : ''
                " :help="form.errors['paymentType.bankName']">
                <a-input v-model:value="form.paymentType.bankName" />
            </a-form-item>
            <a-form-item label="Account Number:" name="acc" :validate-status="form.errors['paymentType.accountNumber'] ? 'error' : ''
                " :help="form.errors['paymentType.accountNumber']">
                <a-input v-model:value="form.paymentType.accountNumber" />
            </a-form-item>
            <a-form-item label="Check Number:" name="check" :validate-status="form.errors['paymentType.checkNumber'] ? 'error' : ''
                " :help="form.errors['paymentType.checkNumber']">
                <a-input v-model:value="form.paymentType.checkNumber" />
            </a-form-item>
        </div>
        <a-form-item v-if="form.paymentType.type != '4'"
            label=" Cash Amount:" name="amount" :validate-status="getErrorStatus('paymentType.amount')"
            :help="getErrorMessage('paymentType.amount')">
            <ant-input-number v-model:amount="form.paymentType.amount"
                @clear-error="clearError('paymentType.amount')" />
        </a-form-item>
    </a-form>
</template>

<script setup lang="ts">
const props = defineProps<{
    form: any;
}>();

const getErrorStatus = (field: string) => {
    return props.form.errors[field] ? "error" : "";
};
const getErrorMessage = (field: string) => {
    return props.form.errors[field];
};
const clearError = (field: string) => {
    props.form.errors[field] = null;
    // console.log(field)
};
</script>

<style lang="scss" scoped></style>
