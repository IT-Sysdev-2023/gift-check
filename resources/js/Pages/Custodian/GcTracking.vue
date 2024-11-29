<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-card >
                        <a-form-item :validate-status="error?.message ? 'error' : ''" has-feedback
                            :help="error?.message">
                            <a-input allow-clear @change="() => error = null" size="large" placeholder="Enter Barcode Here..."
                                v-model:value="barcodeNo"></a-input>
                        </a-form-item>
                        <a-button block class="mt-0" type="primary" @click="submit">
                            Track Barcode
                        </a-button>
                    </a-card>
                </a-col>
                <a-col :span="14">
                    <a-card v-if="gcres" style="border: 1px solid #C2FFC7;">
                        <a-descriptions layout="horizontal" :title="'Barcode ' + gcres.barcode + ' Tracked'">
                            <a-descriptions-item :span="2">
                                <template #label>
                                    <p class="font-bold"> DATE GENERATED</p>
                                </template>
                                {{ gcres.dateGen }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    <p class="font-bold"> DENOMINATION</p>
                                </template>
                                {{ gcres.denom }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    <p class="font-bold"> PRODUCTION #</p>
                                </template>
                                {{ gcres.entry }}
                            </a-descriptions-item>
                        </a-descriptions>
                    </a-card>
                    <a-card v-if="csrrres" class="mt-2" style="border: 1px solid #C2FFC7;">
                        <a-descriptions layout="horizontal">
                            <a-descriptions-item :span="2">
                                <template #label>
                                    <p class="font-bold"> DATE RECEIVED</p>
                                </template>
                                {{ csrrres.datetime }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    <p class="font-bold"> GC RECEIVING #</p>
                                </template>
                                {{ csrrres.num }}
                            </a-descriptions-item>
                            <a-descriptions-item :span="2">
                                <template #label>
                                    <p class="font-bold"> P.O. #</p>
                                </template>
                                {{ csrrres.purono }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    <p class="font-bold"> RECEIVED BY</p>
                                </template>
                                {{ csrrres.name }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    <p class="font-bold"> RECEIVED TYPE</p>
                                </template>
                                {{ csrrres.type }}
                            </a-descriptions-item>
                        </a-descriptions>
                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';

const barcodeNo = ref<number>();

interface GcRes {
    barcode: number,
    dateGen: string,
    denom: string,
    entry: string,
}

interface CsrrRes {
    datetime: string,
    num: number,
    purono: number,
    name: string,
    type: string,
}
interface Error {
    error: string,
    message: string,
}

const gcres = ref<GcRes>();
const csrrres = ref<CsrrRes>();

const error = ref<Error>();

const submit = () => {

    const barcode = barcodeNo.value;

    axios.get(route('custodian.tracking.submit'), {
        params: {
            barcode
        }
    })
        .then(res => {
            gcres.value = res.data.datagc; // Handle success
            csrrres.value = res.data.datasrr; // Handle success
        })
        .catch(err => {
            error.value = err.response.data;
        });
}
</script>
