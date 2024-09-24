<template>
    <a-card>
        <div class="flex justify-between">
            <div>
                <a-space>
                    <a-avatar :size="30">
                        <template #icon>
                            <UserOutlined />
                        </template>
                    </a-avatar>
                </a-space>
            </div>
            <div class="ml-2 mr-2">
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Rem amet error libero suscipit debitis ipsam
                impedit,
                dignissimos officiis cupiditate minus ad! Temporibus ex doloremque, ducimus corrupti animi cupiditate
                odit optio!
            </div>
            <div class="animate-pulse">
                <a-button>
                    <template #icon>
                        <LikeOutlined />
                    </template>
                </a-button>
            </div>
        </div>
    </a-card>
</template>

<script setup>

</script>
