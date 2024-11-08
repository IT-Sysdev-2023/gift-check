<template>
    <a-drawer :width="500" :height="550" title="Edit Form Order Purchase Details" :placement="'top'" @close="onClose">
        <template #extra>
            <a-button style="margin-right: 8px" @click="onClose">Cancel</a-button>
            <a-button type="primary" @click="onClose">Submit</a-button>
        </template>

        <a-row :gutter="[16, 16]">
            <a-col :span="9">
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
            </a-col>
            <a-col :span="9">
                <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item> <a-form-item>
                    <a-input placeholder="Username">
                        <template #prefix>
                            <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                        </template>
                    </a-input>
                </a-form-item>
            </a-col>
            <a-col :span="6">
                <!-- {{denom.denom}} -->
                <a-form-item v-for="denom in denom">
                    <div class="flex">

                        <a-input :value="denom.denomination" style="width: 150px;">
                            <template #prefix>
                                <MoneyCollectOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>

                        <a-input placeholder="Username" :value="denom.qty" @change="handleDenom">
                            <template #prefix>
                                <MoneyCollectOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>

                    </div>
                </a-form-item>
            </a-col>
        </a-row>
    </a-drawer>
</template>
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    denom: []
})
const props = defineProps({
    data: Object,
    denom: Object
})
const emit = defineEmits(['close-drawer']);

const onClose = () => {
    emit('close-drawer')
}

const handleDenom = (denom) => {

    console.log(data)

}

</script>
