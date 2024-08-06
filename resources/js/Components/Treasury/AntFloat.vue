<template>
    <a-float-button-group trigger="click" :style="{ right: '24px' }">
        <template #icon>
            <a-badge dot :offset="[0, -12]">
                <ExclamationCircleOutlined style="font-size: 20px" />
            </a-badge>
            <!-- <ExclamationCircleOutlined v-else /> -->
        </template>
        <a-card class="card-admin-style">
            <template #title>
                <span>Production Request</span>
            </template>
            <a-space direction="vertical">
                <a-alert
                    v-for="(item, index) in $page.props.pendingPrRequest"
                    :key="index"
                    :message="'Rst. no. ' + item.pe_num"
                    description="Production has been approved please click the button to generate barcode"
                    type="info"
                    show-icon
                    style="font-size: 12px"
                >
                    <template #action>
                        <a-space direction="vertical">
                            <a-popconfirm
                                title="Are you sure accept this request?"
                                ok-text="Yes"
                                cancel-text="No"
                                @confirm="acceptRequest(item.pe_id)"
                            >
                                <a-button type="primary">Accept</a-button>
                            </a-popconfirm>
                        </a-space>
                    </template>
                </a-alert>
            </a-space>
        </a-card>
    </a-float-button-group>
</template>
<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { onProgress } from "@/Mixin/UiUtilities";
const { openLeftNotification } = onProgress();
const acceptRequest = (id) => {
    router.get(route("treasury.acceptProdRequest", id),{}, {onSuccess: ({props}) =>{
        openLeftNotification(props.flash);
    }});
};
</script>
<style>
.card-admin-style {
    height: 450px;
    width: 500px;
    right: 480px;
}
</style>
