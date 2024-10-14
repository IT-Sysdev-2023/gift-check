<template>
    <a-card>
        <a-card size="small"  class="mb-2">
            <div class="flex justify-between">
                <div class="mt-1">
                    <GiftTwoTone />
                </div>
                <div class="mt-1">
                    <p>Approved Request</p>
                </div>
                <div>
                    <a-button @click="() => $inertia.visit(route('custodian.production.index'))"><ArrowsAltOutlined /> View</a-button>
                </div>
            </div>
        </a-card>
        <a-card size="small"  class="mb-2">
            <div class="flex justify-between">
                <div class="mt-1">
                    <RestTwoTone />
                </div>
                <div class="mt-1">
                    <p>Cancelled Request</p>
                </div>
                <div>
                    <a-button @click="() => $inertia.visit(route('custodian.production.pro.cancelled'))"><ArrowsAltOutlined /> View</a-button>
                </div>
            </div>
        </a-card>
    </a-card>
</template>
<script setup>

</script>
