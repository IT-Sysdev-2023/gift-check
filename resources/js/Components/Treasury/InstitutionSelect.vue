<template>
    <a-form-item
        label="Payment/Transaction Type:"
        :validate-status="errorForm?.['paymentType.type'] ? 'error' : ''"
        :help="errorForm?.['paymentType.type']"
    >
        <ant-select
            :options="paymentTypeOptions"
            @handle-change="handPaymentType"
        />
    </a-form-item>

    <div v-if="formState.paymentType.type === 'check'">
        <a-form-item label="Bank Name:" name="bank">
            <a-input v-model:value="formState.paymentType.bankName" />
        </a-form-item>
        <a-form-item label="Account Number:" name="acc">
            <a-input v-model:value="formState.paymentType.accountNumber" />
        </a-form-item>
        <a-form-item label="Check Number:" name="check">
            <a-input v-model:value="formState.paymentType.checkNumber" />
        </a-form-item>
        <a-form-item label="Check Amount:" name="check">
            <a-input v-model:value="formState.paymentType.checkAmount" />
        </a-form-item>
    </div>

    <a-form-item
        v-else-if="formState.paymentType.type === 'cash'"
        label="Cash Amount:"
        name="amount"
        :validate-status="errorForm?.['paymentType.amount'] ? 'error' : ''"
        :help="errorForm?.['paymentType.amount']"
    >
        <ant-input-number v-model:amount="formState.paymentType.amount" />
    </a-form-item>
    <a-form-item
        label="Customer:"
        :validate-status="errorForm?.['paymentType.customer'] ? 'error' : ''"
        :help="errorForm?.['paymentType.customer']"
        v-else-if="formState.paymentType.type === 'jv'"
    >
        <ant-select
            :options="customerOptions"
            @handle-change="handleCustomerOption"
        />
    </a-form-item>
</template>

<script setup lang="ts">
const props = defineProps<{
    formState: {
        paymentType: {
            type: string;
            customer: string;
            bankName: string;
            accountNumber: string;
            checkNumber: string;
            checkAmount: string;
            amount: number;
        };
    };
    errorForm: any;
}>();

const emit = defineEmits<{
    (e: "handPaymentType", value: string): void;
    (e: "handleCustomerOption", value: string): void;
}>();
const customerOptions = [
    {
        value: "beam and go",
        label: "Beam and Go",
    },
];
const paymentTypeOptions = [
    {
        value: "cash",
        label: "Cash",
    },
    {
        value: "check",
        label: "Check",
    },
    {
        value: "jv",
        label: "Check and Cash",
    },
    {
        value: "jv",
        label: "Giveaways and Donations",
    },
    {
        value: "jv",
        label: "AR",
    },
];
const handPaymentType = (value: string) => emit("handPaymentType", value);
const handleCustomerOption = (value) => emit("handleCustomerOption", value);
</script>
