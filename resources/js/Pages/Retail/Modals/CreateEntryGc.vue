<template>
    <a-modal  style="width: 80%; top: 40px" :mask-closable="false" class="text-center" title="Create Entry Store" :footer="null">
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="7">
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Receiving No.">{{ record.store
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Received">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Request No">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Requested">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Released No.">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Dated Released">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Released By">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Received Type">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Check By">Cloud Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Received by">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                </a-col>
                <a-col :span="17">
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 20%;" label="Domination">
                            Quantity</a-descriptions-item>
                        <a-descriptions-item style="width: 20%;" label="Subtotal">Scanned</a-descriptions-item>
                        <a-descriptions-item>Action</a-descriptions-item>
                    </a-descriptions>
                    <div v-for="item in record.release">
                        <a-descriptions class="mb-1" size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 20%;" :label="'₱ ' + item[0].denom.toLocaleString()">{{
                                item[0].quantity }}</a-descriptions-item>
                            <a-descriptions-item style="width: 20%;" :label="'₱ ' + item[0].sub.toLocaleString()">{{
                                item[0].scanned }}</a-descriptions-item>
                            <a-descriptions-item>
                                <a-button @click="openDrawer(item)" :disabled="item[0].scanned === item[0].quantity">
                                    <template #icon>
                                        <FastForwardOutlined />
                                    </template>
                                    Scan Gc
                                </a-button>
                            </a-descriptions-item>
                        </a-descriptions>
                    </div>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="10">
                            <span>
                                Total
                            </span>
                            <a-input class="text-center" size="large" readonly :value="record.total" />
                        </a-col>
                        <a-col :span="14">
                        </a-col>
                    </a-row>

                </a-col>
            </a-row>
        </a-card>

        <scan-gc-drawer v-model:open="drawer" @close-drawer="closeDrawer" :record="data" :rec="record.store" />

    </a-modal>




</template>

<script setup>

import { ref } from 'vue';


defineProps({
    record: Object,
})


const drawer = ref(false)

const data = ref({})


const openDrawer = (record) => {
    drawer.value = true;
    data.value = record[0];
}
const closeDrawer = (record) => {
    drawer.value = false;
}


</script>
