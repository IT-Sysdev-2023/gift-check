<template>
    <AuthenticatedLayout>
        <div>
        <a-row :gutter="[16, 16]" class="mb-4">
            <a-col :span="8">
                <a-form-item class="mx-10"  style="font-size: bold;"label="Revolving Fund">
                <a-input  v-bind:value="'₱' + r_fund" readonly />
                </a-form-item>
            </a-col>
        
            <a-col :span="8">
                 <a-form-item class="mx-10"  style="font-size: bold;" label="Total GC">
                 <a-input v-bind:value="'₱' + total" readonly/>
                 </a-form-item>
            </a-col>

            <a-col :span="8">
                <a-form-item  class="mx-5" label="Current Store Budget">
                <a-input max="r_fund" v-bind:value="'₱' + storeBudget" readonly />
                </a-form-item>
            </a-col>
        </a-row>
        </div>
        

            <!-- <div class="flex justify-start " >
                <a-form-item class="mx-10"  style="font-size: bold;" label="Revolving Fund" >
                    <a-input v-bind:value="'₱' + r_fund" readonly />
                </a-form-item>
                <a-form-item class="mx-10" label="Total GC">
                    <a-input v-bind:value="'₱' + total" readonly />
                </a-form-item> -->
                <!-- <a-form-item class="mx-10" label="Current Store Budget">
                    <a-input :max="r_fund" v-bind:value="'₱' + storeBudget" readonly />
                </a-form-item> -->
          
        

        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <m-card
                title="Store Gc Request"
                :pending="countGcRequest.PendingGcRequest"
                :approved="countGcRequest.approved"
                :cancelled="0"
                pRoute="retail.gcrequest.pending.list"
                aRoute="retail.approved.request"
                />
            </a-col>
            <a-col :span="8">
                <available-gc :availableGc :total />
            </a-col>
            <a-col :span="8">
                <sold-gc :soldGc />
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
        soldGc: Object,
        total: Object,
        r_fund: Object,
        storeBudget: Object
    }
}
</script>
