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
        <a-form-item label="Check Amount:" name="anmount">
            <a-input v-model:value="formState.paymentType.amount" />
        </a-form-item>
        <a-form-item
            label="Change:"
            name="changeCheck"
            :validate-status="errorForm?.['paymentType.change'] ? 'error' : ''"
            :help="errorForm?.['paymentType.change']"
        >
            <ant-input-number v-model:amount="formState.paymentType.change" />
        </a-form-item>
    </div>
    <div v-if="formState.paymentType.type === 'cashcheck'">
        <a-form-item label="Bank Name:" name="bank">
            <a-input v-model:value="formState.paymentType.bankName" />
        </a-form-item>
        <a-form-item label="Account Number:" name="acc">
            <a-input v-model:value="formState.paymentType.accountNumber" />
        </a-form-item>
        <a-form-item label="Check Number:" name="check">
            <a-input v-model:value="formState.paymentType.checkNumber" />
        </a-form-item>
        <a-form-item label="Check Amount:" name="anmount">
            <a-input v-model:value="formState.paymentType.amount" />
        </a-form-item>
        <a-form-item label="Cash:" name="cash">
            <a-input v-model:value="formState.paymentType.cash" />
        </a-form-item>
        <a-form-item label="Total Amount Received:" name="cash">
            <a-input
                v-model:value="formState.paymentType.totalAmountReceived"
            />
        </a-form-item>

        <a-form-item
            label="Change:"
            name="changeCheck"
            :validate-status="errorForm?.['paymentType.change'] ? 'error' : ''"
            :help="errorForm?.['paymentType.change']"
        >
            <ant-input-number v-model:amount="formState.paymentType.change" />
        </a-form-item>
    </div>
    <div v-else-if="formState.paymentType.type === 'gad'">
        <a-form-item
            label="Supporting Document:"
            name="sup"
            :validate-status="errorForm?.['paymentType.supDocu'] ? 'error' : ''"
            :help="errorForm?.['paymentType.supDocu']"
        >
            <ant-input-number v-model:amount="formState.paymentType.supDocu" />
        </a-form-item>
    </div>

    <div v-else-if="formState.paymentType.type === 'cash'">
        <a-form-item
            label="Amount Received:"
            name="amount"
            :validate-status="errorForm?.['paymentType.amount'] ? 'error' : ''"
            :help="errorForm?.['paymentType.amount']"
        >
            <ant-input-number v-model:amount="formState.paymentType.amount" />
        </a-form-item>
        <a-form-item
            label="Change:"
            name="change"
            :validate-status="errorForm?.['paymentType.change'] ? 'error' : ''"
            :help="errorForm?.['paymentType.change']"
        >
            <ant-input-number v-model:amount="formState.paymentType.change" />
        </a-form-item>
    </div>
    <div v-else-if="formState.paymentType.type === 'ar'">
        <a-form-item
            label="Amount Received:"
            name="amount"
            :validate-status="errorForm?.['paymentType.amount'] ? 'error' : ''"
            :help="errorForm?.['paymentType.amount']"
        >
            <ant-input-number v-model:amount="formState.paymentType.amount" />
        </a-form-item>
    </div>

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
            amount: number;
            change: number;
            cash: number;
            totalAmountReceived: number;
            supDocu: string
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
        value: "cashcheck",
        label: "Check and Cash",
    },
    {
        value: "gad",
        label: "Giveaways and Donations",
    },
    {
        value: "ar",
        label: "AR",
    },
];
const handPaymentType = (value: string) => emit("handPaymentType", value);
const handleCustomerOption = (value) => emit("handleCustomerOption", value);
</script>
