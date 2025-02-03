<template>
    <div
        class="h-screen bg-[#f3f4f6]"
        style="display: flex; justify-content: center; align-items: center"
    >
        
            <a-row style="
                height: 80%;
                width: 80%;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: white;
                border-radius: 23px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            ">
                <a-col
                    :span="12"
                    class="flex flex-col justify-center items-center"
                >
                    <div
                        style="
                            height: 250px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                        "
                    >
                        <div
                            style="
                                background-image: url(&quot;gcicon.png&quot;);
                                background-size: cover;
                                height: 200px;
                                width: 200px;
                            "
                        ></div>
                    </div>
                    <div
                        style="
                            height: 300px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                        "
                    >
                        <div style="height: 250px; width: 400px">
                            <a-form-item
                                label="Username"
                                has-feedback
                                :help="form.errors.username"
                                :validate-status="
                                    form.errors.username ? 'error' : ''
                                "
                            >
                                <a-input
                                    @keyup.enter="login"
                                    v-model:value="form.username"
                                    @change="form.errors.username = ''"
                                />
                            </a-form-item>
                            <a-form-item
                                label="Password"
                                has-feedback
                                :help="form.errors.password"
                                :validate-status="
                                    form.errors.password ? 'error' : ''
                                "
                            >
                                <a-input
                                    @keyup.enter="login"
                                    type="password"
                                    v-model:value="form.password"
                                    @change="form.errors.password = ''"
                                />
                            </a-form-item>
                            <div>
                                <div class="flex justify-center">
                                    <a-button
                                        :loading="loading"
                                        @click="login"
                                        style="width: 200px"
                                        type="primary"
                                        >Login</a-button
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </a-col>
                <a-col class="bg-white flex justify-center" :span="12">
                    <div
                        ref="animationContainer"
                        class="p-12 w-[600px] h-full bg-center relative"
                    ></div>
                </a-col>
            </a-row>
            <!-- <div class="bg-sky-200">
                
            </div> -->
        </div>
  
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const loading = ref(false);

const animationContainer = ref(null);

onMounted(() => {
    if (window.lottie) {
        window.lottie.loadAnimation({
            container: animationContainer.value, // The container div
            renderer: "svg",
            loop: true,
            autoplay: true,
            path: "/images/login-anim.json", // Path to your animation JSON file
        });
    }
});
const form = useForm({
    username: "",
    password: "",
});

const login = () => {
    form.post(route("login"), {
        onStart: () => {
            loading.value = true;
        },
        onSuccess: () => {
            form.reset();
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        },
    });
};
</script>
