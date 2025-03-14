<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <m-card title="Special Gc Request" aRoute="custodian.approved.request" pRoute="accounting.pending.index"
                    :pending="count.pending">
                    <a-button style="width: 340px" type="primary" class="mb-2"
                        @click="router.get(route('accounting.payment.payment.gc'))">
                        Payment Gc
                    </a-button>
                    <a-button style="width: 340px" type="primary" class="mb-2"
                        @click="router.get(route('accounting.payment.viewing'))">
                        Payment Viewing
                    </a-button>
                </m-card>
            </a-col>
            <a-col :span="8">
                <a-card title="Institution GC Released" class="mb-5 uppercase">
                    <inner-m-card label="Transactions" routeTo="treasury.transactions.institution.gc.sales.transaction"
                        :count="data?.institutionGcSales" />
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup>
import { router } from '@inertiajs/core';
const props = defineProps({
    count: Object
})
</script>
