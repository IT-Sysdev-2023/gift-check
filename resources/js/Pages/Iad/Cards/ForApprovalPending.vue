<template>
    <div class="task">
        <div class="tags text-center">
            <a-tag>
                Budget Request Approval
            </a-tag>
        </div>
    </div>
    <a-modal v-model:open="open" title="Basic Modal" style="width: 60%;">
        <iframe :src="stream" frameborder="2" style="width: 100%; height: 400px;"></iframe>
    </a-modal>

    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Budget Requested"> ₱ {{ budget.br_request
        }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Requested At">{{ budget.datereq }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Remarks">{{ budget.br_remarks }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Requested By">{{ budget.reqby }}</a-descriptions-item>
    </a-descriptions>
    <a-button class="mt-4" block @click="approve(budget.br_id)">
        <LikeOutlined />Approve Budget Request
    </a-button>

</template>

<script setup>
import { router } from '@inertiajs/core';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
import axios from 'axios';

const emit = defineEmits(['close']);

const props = defineProps({
    budget: Object
});
const stream = ref();
const open = ref(false);

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
            emit('close');
        },
        preserveState: true,

    })
}


</script>
<style scoped>
/* From Uiverse.io by Yaya12085 */
.task {
    position: relative;
    cursor: move;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
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
