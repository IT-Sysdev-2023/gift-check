<template>
    <!-- {{ record }} -->
    <a-row :gutter="[16, 16]">  
        <a-col :span="14">
            <special-gc-setup-details :record="record" />
        </a-col>
        <a-col :span="10">
            <a-card>
                <p class="mb-3">
                    <span class="" style="color: #3795BD;">
                        Entry By:
                    </span>
                    <a-typography-text keyboard>{{ $page.props.auth.user.full_name }}</a-typography-text>
                </p>

                <a-card size=small class=" mb-4">
                    <a-statistic title="Total Denomination" :value="record[0]?.total" :precision="2"
                        class="demo-class text-center" :value-style="{ color: '#cf1322' }">
                        <template #prefix>
                            <DollarCircleOutlined />
                        </template>
                    </a-statistic>
                </a-card>
                <a-row :gutter="[16, 16]"  v-for="assign in record[0].special_external_gcrequest_items_has_many" class="mb-4">
                    <a-col :span="8">
                        <span>Denomination</span>
                        <a-input prefix="#" :value="assign.specit_denoms" readonly />

                    </a-col>
                    <a-col :span="7">
                        <span>Quantity</span>
                        <a-input prefix="#" :value="assign.specit_qty" readonly />

                    </a-col>
                    <a-col :span="6">
                        <span>Holder</span>
                        <a-input prefix="#" readonly :value="1" />

                    </a-col>
                    <a-col :span="3">
                        <br>
                        <a-button>
                            <template #icon>
                                <a-tooltip>
                                    <template #title>Assign Customer Employee</template>
                                    <UsergroupAddOutlined />
                                </a-tooltip>
                            </template>
                        </a-button>
                    </a-col>

                </a-row>
            </a-card>
        </a-col>
    </a-row>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
    }
}
</script>
