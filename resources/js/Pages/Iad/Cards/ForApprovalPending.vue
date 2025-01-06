<template>
    <!-- <a-card v-if="budget" style="border: 1px solid #73EC8B;">
        <div class="flex justify-between">
            <div class="ml-2 mr-2 mt-1">
                <span >
                    Requested Budget:
                </span> &nbsp; <span class="font-bold">₱{{ budget?.br_request.toLocaleString() }}</span>
            </div>
            <div class="animate-pulse">
                <a-button @click="openDrawer(budget.br_id)">
                    <template #icon>
                        <LikeOutlined />
                    </template>
Approve
</a-button>
</div>
</div>
</a-card> -->
    <div class="task">
        <div class="tags">
            <span class="tag">Budget Request Approval</span>

        </div>
        <p>
        <div class="ml-2 mb-7 mr-2 mt-7 p-2">
            <span>
                Requested Budget:
            </span> &nbsp; <span class="font-bold">₱{{ budget?.br_request.toLocaleString() }}</span>
        </div>
        </p>
        <div>
            <div>
                <a-button type="primary" ghost block @click="openDrawer(budget.br_id)">
                    <template #icon>
                        <LikeOutlined />
                    </template>
                    Approve Request
                </a-button>

            </div>

        </div>

    </div>

    <a-modal v-model:open="open" title="Basic Modal" style="width: 60%;">
        <iframe :src="stream" frameborder="2" style="width: 100%; height: 400px;"></iframe>
    </a-modal>

    <a-modal :footer="null" :width="500" title="Approve Budget Request" v-model:open="drawer" @close="onClose">
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Budget Requested"> ₱ {{ data.br_request.toLocaleString()
                }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Requested At">{{ data.requested }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Remarks">{{ data.br_remarks }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Requested By">{{ data.reqby }}</a-descriptions-item>
        </a-descriptions>
        <a-button class="mt-4" block @click="approve(budget.br_id)">
            <LikeOutlined />Approve Budget Request
        </a-button>
    </a-modal>

</template>

<script setup>
import { router } from '@inertiajs/core';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    budget: Object
});
const stream = ref();
const open = ref(false);
const data = ref({});
const drawer = ref(false);

const approve = (id) => {
    router.put(route('iad.approve', id), {
    }, {
        onSuccess: (res) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });
            stream.value = `data:application/pdf;base64,${res.props.flash.stream}`;
            open.value = true;
            drawer.value = false;
        },
        preserveState: true,
    })
}

const openDrawer = (id) => {
    axios.get(route('iad.details', id)).then(res => {
        data.value = res.data.record;
        drawer.value = true;
    })
}

const onClose = () => {
    drawer.value = false;
}

</script>
<style scoped>
/* From Uiverse.io by Yaya12085 */
.task {
    position: relative;
    color: #2e2e2f;
    cursor: move;
    background-color: #46ca0909;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 8px 0px;
    margin-bottom: 1rem;
    border: 1px solid rgba(129, 255, 55, 0.356);
    max-width: 350px;
}

.task:hover {
    box-shadow: rgba(99, 99, 99, 0.3) 0px 2px 8px 0px;
    border-color: rgba(162, 179, 207, 0.2) !important;
}

.task p {
    font-size: 15px;
    margin: 1.2rem 0;
}

.tag {
    border-radius: 100px;
    padding: 4px 13px;
    font-size: 12px;
    color: #ffffff;
    background-color: #1389eb;
}

.tags {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.options {
    background: transparent;
    border: 0;
    color: #c4cad3;
    font-size: 17px;
}

.options svg {
    fill: #9fa4aa;
    width: 20px;
}

.stats {
    position: relative;
    width: 100%;
    color: #9fa4aa;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stats div {
    margin-right: 1rem;
    height: 20px;
    display: flex;
    align-items: center;
    cursor: pointer;
}

.stats svg {
    margin-right: 5px;
    height: 100%;
    stroke: #9fa4aa;
}

.viewer span {
    height: 30px;
    width: 30px;
    background-color: rgb(28, 117, 219);
    margin-right: -10px;
    border-radius: 50%;
    border: 1px solid #fff;
    display: grid;
    align-items: center;
    text-align: center;
    font-weight: bold;
    color: #fff;
    padding: 2px;
}

.viewer span svg {
    stroke: #fff;
}
</style>
