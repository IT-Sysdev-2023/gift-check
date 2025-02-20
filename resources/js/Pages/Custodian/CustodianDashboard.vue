<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <m-card  aRoute="custodian.approved.request" pRoute="custodian.pendings.holder.entry" :pending="count.countIntExRequest" :approved="count.countApproved" class="uppercase"
                    title="special external gc request">

                    <a-badge class="mb-2" :count="count.countReleased" :overflow-count="Infinity"
                        :number-style="{ backgroundColor: '#1677ff' }">
                        <a-button  style="width: 340px"  type="primary" block @click="() => router.get(route('custodian.released'))">
                            Released
                        </a-button>
                    </a-badge>
                </m-card>

                <!-- <special-external-gc-request :count="count" /> -->
            </a-col>
            <a-col :span="8">
                <production-request />
            </a-col>
            <a-col :span="8">
                <available-gc-allocation-card :denom="denom" />
            </a-col>
        </a-row>
        <div class="mt-5">
            <a-row>
                <a-col :span="8">

                    <MCard title="DTI Special GC Transactions" :pending="pending"
                        pRoute="custodian.dti_special_gcdti_special_gc_pending" />
                </a-col>
            </a-row>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MCard from '../Marketing/Card/MCard.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/core'
defineProps({
    count: Array,
    denom: Object,
})

const pending = ref(0)

const getDTIRequestCount = () => {
    axios.get(route('custodian.dti_special_gcdti_special_gc_count'))
        .then(response => {
            console.log(response.data)
            pending.value = response.data.pending
        })
        .catch(error => {
            console.log(error)
        })
}

getDTIRequestCount()

</script>
