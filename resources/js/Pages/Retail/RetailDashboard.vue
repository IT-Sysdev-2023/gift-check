<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <store-gc-request-card :counts="countGcRequest" />
            </a-col>
            <a-col :span="8">
                <available-gc :availableGc :total/>
            </a-col>
            <a-col :span="8">
                <a-form-item label="Revolving Fund">
                    <a-input v-bind:value="r_fund" readonly/>
                </a-form-item>
                <a-form-item label="Current Store Budget">
                    <a-input :max="r_fund" v-bind:value="storeBudget" readonly/>
                </a-form-item>
                <sold-gc :soldGc/>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script>
import AvailableGc from './Cards/AvailableGc.vue';

export default {
    props: {
        countGcRequest: Object,
        availableGc: Object,
        soldGc:Object,
        total : Object,
        r_fund:Object,
        storeBudget:Object
    }
}
</script>
