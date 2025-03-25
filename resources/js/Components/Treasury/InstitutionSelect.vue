<template>
    <a-form-item
        label="Payment/Transaction Type:"
        :validate-status="formState.errors?.['paymentType.type'] ? 'error' : ''"
        :help="formState.errors?.['paymentType.type']"
    >
        <ant-select
            :options="paymentTypeOptions"
            @handle-change="handPaymentType"
        />
    </a-form-item>

    <div v-if="formState.paymentType.type === 'check'">
        <a-form-item
            label="Bank Name:"
            name="bank"
            :validate-status="
                formState.errors?.['paymentType.bankName'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.bankName']"
        >
            <a-input v-model:value="formState.paymentType.bankName" />
        </a-form-item>
        <a-form-item
            label="Account Number:"
            name="acc"
            :validate-status="
                formState.errors?.['paymentType.accountNumber'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.accountNumber']"
        >
            <a-input v-model:value="formState.paymentType.accountNumber" />
        </a-form-item>
        <a-form-item
            label="Cheque Number:"
            name="check"
            :validate-status="
                formState.errors?.['paymentType.checkNumber'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.checkNumber']"
        >
            <a-input-number
                v-model:value="formState.paymentType.checkNumber"
                style="width: 100%"
            />
        </a-form-item>
        <a-form-item
            label="Cheque Amount:"
            name="anmount"
            :validate-status="
                formState.errors?.['paymentType.amount'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.amount']"
        >
            <ant-input-number v-model:value="formState.paymentType.amount" />
        </a-form-item>
        <a-form-item label="Change:" name="changeChe">
            <ant-input-number :amount="changeForAmount" readonly />
        </a-form-item>
    </div>
    <div v-if="formState.paymentType.type === 'cashcheck'">
        <a-form-item
            label="Bank Name:"
            name="bank"
            :validate-status="
                formState.errors?.['paymentType.bankName'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.bankName']"
        >
            <a-input v-model:value="formState.paymentType.bankName" />
        </a-form-item>
        <a-form-item
            label="Account Number:"
            name="acc"
            :validate-status="
                formState.errors?.['paymentType.accountNumber'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.accountNumber']"
        >
            <a-input v-model:value="formState.paymentType.accountNumber" />
        </a-form-item>
        <a-form-item
            label="Check Number:"
            name="check"
            :validate-status="
                formState.errors?.['paymentType.checkNumber'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.checkNumber']"
        >
            <a-input v-model:value="formState.paymentType.checkNumber" />
        </a-form-item>
        <a-form-item
            label="Check Amount:"
            name="anmount"
            :validate-status="
                formState.errors?.['paymentType.amount'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.amount']"
        >
            <ant-input-number v-model:amount="formState.paymentType.amount" />
        </a-form-item>
        <a-form-item
            label="Cash:"
            name="cash"
            :validate-status="
                formState.errors?.['paymentType.cash'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.cash']"
        >
            <ant-input-number v-model:amount="formState.paymentType.cash" />
        </a-form-item>
        <a-form-item label="Total Amount Received:" name="cash">
            <ant-input-number
                :value="
                    formState.paymentType.cash + formState.paymentType.amount
                "
                readonly
            />
        </a-form-item>

        <a-form-item
            label="Change:"
            name="changeCh"
            :validate-status="
                formState.errors?.['paymentType.change'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.change']"
        >
            <ant-input-number
                :amount="
                    formState.paymentType.cash +
                    formState.paymentType.amount -
                    total
                "
                readonly
            />
        </a-form-item>
    </div>
    <div v-else-if="formState.paymentType.type === 'gad'">
        <a-form-item
            label="Supporting Document:"
            name="sup"
            :validate-status="
                formState.errors?.['paymentType.supDocu'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.supDocu']"
        >
            <a-input v-model:value="formState.paymentType.supDocu" />
        </a-form-item>
    </div>

    <div v-else-if="formState.paymentType.type === 'cash'">
        <a-form-item
            label="Amount Received:"
            name="amount"
            :validate-status="
                formState.errors?.['paymentType.amount'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.amount']"
        >
            <ant-input-number v-model:amount="formState.paymentType.amount" />
        </a-form-item>
        <a-form-item
            label="Change:"
            name="change"
            :validate-status="
                formState.errors?.['paymentType.change'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.change']"
        >
            <ant-input-number :amount="changeForAmount" readonly />
        </a-form-item>
    </div>
    <div v-else-if="formState.paymentType.type === 'ar'">
        <a-form-item
            label="Amount Received:"
            name="amount"
            :validate-status="
                formState.errors?.['paymentType.amount'] ? 'error' : ''
            "
            :help="formState.errors?.['paymentType.amount']"
        >
            <ant-input-number v-model:amount="formState.paymentType.amount" />
        </a-form-item>
    </div>

    <a-form-item
        label="Customer:"
        :validate-status="
            formState.errors?.['paymentType.customer'] ? 'error' : ''
        "
        :help="formState.errors?.['paymentType.customer']"
        v-else-if="formState.paymentType.type === 'jv'"
    >
        <ant-select
            :options="customerOptions"
            @handle-change="handleCustomerOption"
        />
    </a-form-item>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
    formState: {
        errors: any;
        paymentType: {
            type: string;
            customer: string;
            bankName: string;
            accountNumber: string;
            checkNumber: string;
            amount: number;
            cash: number;
            supDocu: string;
        };
    };
    total: number;
}>();

const changeForAmount = computed(() => {
    return props.formState.paymentType.amount - props.total;
});
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
        label: "Cheque",
    },
    {
        value: "cashcheck",
        label: "Cheque and Cash",
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
