<?php
include_once 'koneksi.php';
include_once 'header.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '<script>
        window.location.replace("' . $alamat_website . 'home");
    </script>';
    exit();
}
?>
<section class="container flex mx-auto lg:pt-3 lg:pb-5 lg:mt-5">
    <div class="w-full lg:w-1/3 px-3 hidden lg:block">
        <a class="px-3 py-4 bg-background-default lg:bg-background-secondary rounded-xl mt-4 drop-shadow-[0_0_5px_rgba(0,0,0,0.6)] lg:drop-shadow-none group transition-all duration-300 ease-in-out lg:hover:bg-black/30" href="#">
            <figure class="flex flex-none items-center justify-center w-12 md:w-16 h-12 md:h-16">
                <img alt="VIP Level Badge" width="0" height="0" decoding="async" data-nimg="1" class="w-full" style="color: transparent;" loading="lazy" src="https://cdn.databerjalan.com/assets/images/store/2024-07-11T06:15:28.250Z_Pemain_Baru_1.png"></figure>
            <article class="w-full pl-4">
                <p class="text-sm md:text-base group-hover:text-white">Pemain Baru</p>
                <progress class="w-full h-[5px] primary-progress" value="0" max="100"></progress>
                <span class="text-xs md:text-sm group-hover:text-white">Increase your level and get rewards</span>
            </article>
            <figure class="pl-2"><svg width="26" height="26" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="26">
                    <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                </svg>
            </figure>
        </a>
        <section class="bg-background-secondary rounded-xl mt-4">
            <div class="w-full lg:px-4 pt-3 flex flex-wrap px-4">
                <article class="w-full flex items-center mb-1 lg:mb-3">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.879 3.879C2 4.757 2 6.172 2 9v6c0 2.828 0 4.243.879 5.121C3.757 21 5.172 21 8 21h10c.93 0 1.395 0 1.776-.102a3 3 0 0 0 2.122-2.122C22 18.395 22 17.93 22 17h-6a3 3 0 1 1 0-6h6V9c0-2.828 0-4.243-.879-5.121C20.243 3 18.828 3 16 3H8c-2.828 0-4.243 0-5.121.879ZM7 7a1 1 0 0 0 0 2h3a1 1 0 1 0 0-2H7Z" fill="var(--primary)"></path>
                        <path d="M17 14h-1" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                    <span class="text-xs lg:text-sm text-caption px-2">Account Balance</span>
                    <button>
                        <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="3.5" stroke="var(--caption)"></circle>
                            <path d="M20.188 10.934c.388.472.582.707.582 1.066 0 .359-.194.594-.582 1.066C18.768 14.79 15.636 18 12 18c-3.636 0-6.768-3.21-8.188-4.934-.388-.472-.582-.707-.582-1.066 0-.359.194-.594.582-1.066C5.232 9.21 8.364 6 12 6c3.636 0 6.768 3.21 8.188 4.934Z" stroke="var(--caption)"></path>
                        </svg>
                    </button>
                </article>
                <div class="w-full flex lg:gap-x-5">
                    <div class="w-full flex items-center">
                        <section class="w-full flex items-center h-7">
                            <span class="text-sm lg:text-base font-semibold">IDR&nbsp;<?php echo number_format($_SESSION['saldo_anggota'], 0, ',', '.'); ?></span>
                        <button class="rounded-full bg-background-default cursor-pointer rotate-270 w-7 h-7 ml-2 items-center justify-center flex">
                            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="m10 19-.707-.707-.707.707.707.707L10 19Zm3.293-4.707-4 4 1.414 1.414 4-4-1.414-1.414Zm-4 5.414 4 4 1.414-1.414-4-4-1.414 1.414Z" fill="var(--caption)"></path>
                                    <path d="M5.938 15.5A7 7 0 1 1 12 19" stroke="var(--caption)" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                            </button>
                        </section>
                    </div>
                </div>
            </div>
            <div class="flex gap-x-4 px-4 pb-6 mt-5">
                <a class="w-full justify-center py-2 rounded-lg text-primary border border-primary transition-all duration-200 ease-in-out hover:lg:bg-background-tertiary" href="<?php echo $alamat_website . 'withdraw'; ?>">Withdraw</a>
                <a class="w-full justify-center py-2 rounded-lg bg-primary text-white transition-all duration-200 ease-in-out hover:lg:brightness-90" href="<?php echo $alamat_website . 'deposit'; ?>">Deposit</a>
            </div>
        </section>
        <section class="bg-background-secondary rounded-xl mt-4 overflow-hidden"><a class="justify-between px-4 py-3 hover:lg:bg-background-tertiary transition-all duration-300 ease-in-out" href="<?php echo $alamat_website . 'my-account'; ?>">
                <div class="flex items-center w-[calc(100%-24px)] pr-1">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.002 10h19.996c-.012-2.175-.108-3.353-.877-4.121C20.243 5 18.828 5 16 5H8c-2.828 0-4.243 0-5.121.879-.769.768-.865 1.946-.877 4.121ZM22 12H2v2c0 2.828 0 4.243.879 5.121C3.757 20 5.172 20 8 20h8c2.828 0 4.243 0 5.121-.879C22 18.243 22 16.828 22 14v-2ZM7 15a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H7Z" fill="var(--primary)"></path>
                    </svg>
                    <span class="text-sm pl-2 undefined text-primary">My Account</span>
                </div>
                <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="22">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--primary)"></path>
                    </svg>
                </figure>
                <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="22">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg>
                </figure>
            </a>
        </section>
        <section class="bg-background-secondary rounded-xl mt-4 overflow-hidden"><a class="justify-between px-4 py-3 hover:lg:bg-background-tertiary transition-all duration-300 ease-in-out" href="<?php echo $alamat_website . 'change-pasword'; ?>">
                <div class="flex items-center w-[calc(100%-24px)] pr-1">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.984 2.542c.087.169.109.386.152.82.082.82.123 1.23.295 1.456a1 1 0 0 0 .929.384c.28-.037.6-.298 1.238-.82.337-.277.506-.415.687-.473a1 1 0 0 1 .702.035c.175.076.33.23.637.538l.894.894c.308.308.462.462.538.637a1 1 0 0 1 .035.702c-.058.181-.196.35-.472.687-.523.639-.784.958-.822 1.239a1 1 0 0 0 .385.928c.225.172.636.213 1.457.295.433.043.65.065.82.152a1 1 0 0 1 .47.521c.071.177.071.395.071.831v1.264c0 .436 0 .654-.07.83a1 1 0 0 1-.472.522c-.169.087-.386.109-.82.152-.82.082-1.23.123-1.456.295a1 1 0 0 0-.384.929c.038.28.299.6.821 1.238.276.337.414.505.472.687a1 1 0 0 1-.035.702c-.076.175-.23.329-.538.637l-.894.893c-.308.309-.462.463-.637.538a1 1 0 0 1-.702.035c-.181-.058-.35-.196-.687-.472-.639-.522-.958-.783-1.238-.82a1 1 0 0 0-.929.384c-.172.225-.213.635-.295 1.456-.043.434-.065.651-.152.82a1 1 0 0 1-.521.472c-.177.07-.395.07-.831.07h-1.264c-.436 0-.654 0-.83-.07a1 1 0 0 1-.522-.472c-.087-.169-.109-.386-.152-.82-.082-.82-.123-1.23-.295-1.456a1 1 0 0 0-.928-.384c-.281.037-.6.298-1.239.82-.337.277-.506.415-.687.473a1 1 0 0 1-.702-.035c-.175-.076-.33-.23-.637-.538l-.894-.894c-.308-.308-.462-.462-.538-.637a1 1 0 0 1-.035-.702c.058-.181.196-.35.472-.687.523-.639.784-.958.821-1.239a1 1 0 0 0-.384-.928c-.225-.172-.636-.213-1.457-.295-.433-.043-.65-.065-.82-.152a1 1 0 0 1-.47-.521C2 13.286 2 13.068 2 12.632v-1.264c0-.436 0-.654.07-.83a1 1 0 0 1 .472-.522c.169-.087.386-.109.82-.152.82-.082 1.231-.123 1.456-.295a1 1 0 0 0 .385-.928c-.038-.281-.3-.6-.822-1.24-.276-.337-.414-.505-.472-.687a1 1 0 0 1 .035-.702c.076-.174.23-.329.538-.637l.894-.893c.308-.308.462-.463.637-.538a1 1 0 0 1 .702-.035c.181.058.35.196.687.472.639.522.958.783 1.238.821a1 1 0 0 0 .93-.385c.17-.225.212-.635.294-1.456.043-.433.065-.65.152-.82a1 1 0 0 1 .521-.471c.177-.07.395-.07.831-.07h1.264c.436 0 .654 0 .83.07a1 1 0 0 1 .522.472ZM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="var(--primary)"></path>
                    </svg>
                    <span class="text-sm pl-2 undefined false">Account Settings</span>
                </div>
                <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="22">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg>
                </figure>
            </a>
        </section>
        <a href="<?php echo $alamat_website . 'logout'; ?>" class="block">
            <div class="bg-background-secondary rounded-xl mt-4 overflow-hidden">
                <section class="justify-between px-4 py-3 flex cursor-pointer hover:lg:bg-background-tertiary transition-all duration-300 ease-in-out " href="<?php echo $alamat_website . 'logout'; ?>">
                    <div class="flex items-center w-[calc(100%-24px)] pr-1">
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                            <path d="m2 12-.78-.625-.5.625.5.625L2 12Zm9 1a1 1 0 1 0 0-2v2ZM5.22 6.375l-4 5 1.56 1.25 4-5-1.56-1.25Zm-4 6.25 4 5 1.56-1.25-4-5-1.56 1.25ZM2 13h9v-2H2v2ZM13.342 20.557l.165-.986-.165.986Zm7.597.19.646.764-.646-.763ZM15.014 3.165l-.165-.986.165.986Zm5.925.088.646-.763-.646.763ZM13.507 4.43l1.671-.278-.329-1.973-1.671.279.329 1.972ZM21 9.083v5.834h2V9.083h-2Zm-5.822 10.766-1.671-.278-.329 1.973 1.671.278.329-1.973ZM11 8.132v-.743H9v.743h2Zm0 8.48v-.546H9v.546h2Zm2.507 2.959c-.824-.138-1.35-.227-1.734-.342-.358-.106-.472-.201-.536-.277l-1.526 1.293c.41.484.932.735 1.491.901.532.159 1.203.269 1.976.398l.329-1.973ZM9 16.61c0 .784-.002 1.464.067 2.015.072.578.234 1.135.644 1.619l1.526-1.293c-.064-.075-.14-.203-.185-.574-.05-.398-.052-.932-.052-1.767H9Zm12-1.694c0 1.675-.002 2.823-.123 3.67-.116.82-.32 1.174-.584 1.398l1.293 1.526c.797-.675 1.123-1.593 1.272-2.642.144-1.021.142-2.338.142-3.952h-2Zm-6.15 6.905c1.59.265 2.89.484 3.92.51 1.06.025 2.018-.146 2.816-.821l-1.293-1.526c-.264.223-.646.367-1.474.347-.856-.021-1.99-.207-3.641-.483l-.329 1.973Zm.328-17.671c1.652-.275 2.785-.462 3.64-.483.829-.02 1.21.124 1.475.347l1.293-1.526c-.797-.675-1.757-.846-2.816-.82-1.03.025-2.33.244-3.92.509l.328 1.973ZM23 9.083c0-1.614.002-2.93-.142-3.952-.15-1.049-.476-1.967-1.273-2.642l-1.292 1.526c.264.224.468.577.584 1.397.12.848.123 1.996.123 3.67h2Zm-9.822-6.626c-.773.128-1.444.238-1.976.397-.559.166-1.081.417-1.491.901l1.526 1.293c.064-.076.178-.17.536-.277.384-.115.91-.204 1.734-.342l-.329-1.972ZM11 7.389c0-.835.002-1.369.052-1.767.046-.37.121-.499.185-.574L9.711 3.755c-.41.484-.572 1.04-.644 1.62C8.998 5.924 9 6.604 9 7.388h2Z" fill="var(--primary)"></path>
                        </svg>
                        <span class="text-sm pl-2 undefined undefined">Logout</span>
                    </div>
                    <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="22">
                            <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                        </svg>
                    </figure>
                </section>
            </div>
            <!--$-->
            <!--/$-->
        </div>
    </a>
<div class="w-full lg:w-2/3 lg:px-5">
	<div class="px-2 my-2 lg:hidden">
		<a href="<?php echo $alamat_website . 'my-account'; ?>"><figure class="h-auto w-6 rotate-180"><svg width="100%" height="100%" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg"><path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path></svg></figure><span class="text-sm">Back</span></a>
	</div>
	<div class="grid lg:flex lg:gap-x-6 grid-cols-2 lg:mb-3">
		<a aria-label="My Profile-tab-button" aria-labelledby="My Profile-tab-button" class="text-sm border-b uppercase flex-none px-1 pb-2 justify-center cursor-pointer transition-all duration-300 ease-in-out hover:lg:border-primary hover:lg:text-inverse font-semibold border-transparent text-caption  " href="<?php echo $alamat_website . 'my-account'; ?>">My Profile</a>
        <a aria-label="Change Password-tab-button" aria-labelledby="Change Password-tab-button" class="text-sm border-b uppercase flex-none px-1 pb-2 justify-center cursor-pointer transition-all duration-300 ease-in-out hover:lg:border-primary hover:lg:text-inverse font-semibold border-primary  " href="<?php echo $alamat_website . 'change-pasword'; ?>">Change Password</a>
	</div>
        <div class="lg:hidden sm:absolute sm:left-0 sm:right-0 sm:bg-background-tertiary sm:bottom-0 sm:top-[calc(122px+40px+30px)]"></div>
        <section class="pt-5 pb-3 lg:pb-10 px-4 bg-background-tertiary lg:rounded-xl min-h-[82vh] lg:min-h-min">
            <div class="lg:w-1/2 lg:mx-auto">
                <div class="w-16 lg:w-24 h-16 lg:h-24 mx-auto">
                    <svg width="75" height="75" viewbox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M35.5 67.957c17.926 0 32.457-14.531 32.457-32.457 0-17.925-14.531-32.457-32.457-32.457-17.925 0-32.457 14.532-32.457 32.457 0 17.926 14.532 32.457 32.457 32.457Z" fill="#7E869E"></path>
                        <path opacity="0.1" d="M65.49 41.01 42.983 18.503c-1.663-2.363-4.402-3.915-7.506-3.915-5.051 0-9.164 4.113-9.164 9.164v7.425h-.755a2.736 2.736 0 0 0-2.734 2.738v17.73c0 .776.325 1.476.847 1.973h-.005l14.24 14.24C52.33 66.8 64.128 56.326 67.18 42.552a9.247 9.247 0 0 0-1.69-1.54Z" fill="#000"></path>
                        <path d="M35.476 14.589c-5.052 0-9.165 4.112-9.165 9.164V36.52h18.334V23.753c0-5.052-4.112-9.164-9.169-9.164Zm5.332 18.098H30.144v-8.933a5.341 5.341 0 0 1 5.332-5.332 5.339 5.339 0 0 1 5.332 5.331v8.934Z" fill="#344D5B"></path>
                        <path d="M35.476 16.733c-3.966 0-7.192 3.443-7.192 7.41v10.015h14.388V24.142c0-3.966-3.23-7.409-7.196-7.409Zm5.332 15.954H30.144v-8.933a5.342 5.342 0 0 1 5.332-5.333 5.339 5.339 0 0 1 5.332 5.332v8.934Z" fill="#293B44"></path>
                        <path d="M45.44 31.178H25.56a2.736 2.736 0 0 0-2.737 2.736v17.733a2.736 2.736 0 0 0 2.736 2.736h19.882a2.736 2.736 0 0 0 2.736-2.736V33.914a2.736 2.736 0 0 0-2.736-2.736Z" fill="#FEDA6F"></path>
                        <path d="M25.56 51.952a.305.305 0 0 1-.306-.305V33.914c0-.168.137-.305.305-.305h19.882c.168 0 .305.137.305.305v17.733a.305.305 0 0 1-.305.305H25.56Z" fill="#F9C74B"></path>
                        <path d="M26.587 37.923h17.826v6.213H26.587v-6.213Z" fill="#EDEDED"></path>
                        <path d="m28.36 40.962.171-.526c.393.138.679.258.857.36a12.351 12.351 0 0 1-.074-.924h.538a9.27 9.27 0 0 1-.086.92 6.44 6.44 0 0 1 .876-.356l.17.526a4.615 4.615 0 0 1-.923.208c.15.131.363.365.638.701l-.445.316a9.195 9.195 0 0 1-.509-.798 5.979 5.979 0 0 1-.482.798l-.438-.316c.287-.353.493-.587.616-.7a13.35 13.35 0 0 1-.909-.209ZM31.317 40.962l.17-.526c.394.138.68.258.858.36a12.351 12.351 0 0 1-.074-.924h.538a9.235 9.235 0 0 1-.086.92 6.44 6.44 0 0 1 .876-.356l.17.526a4.614 4.614 0 0 1-.923.208c.15.131.363.365.638.701l-.445.316a9.195 9.195 0 0 1-.509-.798 5.979 5.979 0 0 1-.482.798l-.438-.316c.287-.353.492-.587.616-.7-.319-.063-.622-.132-.909-.209ZM34.274 40.962l.17-.526c.394.138.68.258.857.36a12.351 12.351 0 0 1-.074-.924h.538a9.24 9.24 0 0 1-.085.92 6.44 6.44 0 0 1 .875-.356l.17.526a4.615 4.615 0 0 1-.923.208c.151.131.364.365.638.701l-.445.316a9.195 9.195 0 0 1-.508-.798 5.979 5.979 0 0 1-.483.798l-.437-.316c.287-.353.492-.587.616-.7a13.363 13.363 0 0 1-.91-.209ZM37.23 40.962l.171-.526c.393.138.679.258.857.36a12.351 12.351 0 0 1-.074-.924h.538a9.27 9.27 0 0 1-.086.92 6.44 6.44 0 0 1 .876-.356l.17.526a4.615 4.615 0 0 1-.923.208c.15.131.364.365.638.701l-.445.316a9.195 9.195 0 0 1-.508-.798 5.979 5.979 0 0 1-.483.798l-.437-.316c.286-.353.492-.587.615-.7a13.35 13.35 0 0 1-.909-.209ZM40.188 40.962l.17-.526c.394.138.68.258.857.36a12.351 12.351 0 0 1-.074-.924h.538a9.235 9.235 0 0 1-.085.92c.255-.129.546-.247.875-.356l.171.526a4.614 4.614 0 0 1-.924.208c.151.131.364.365.638.701l-.445.316a9.222 9.222 0 0 1-.508-.798 5.979 5.979 0 0 1-.482.798l-.438-.316c.287-.353.492-.587.616-.7-.32-.063-.622-.132-.91-.209Z" fill="#000"></path>
                        <path d="M75 59c0 6.627-5.373 12-12 12s-12-5.373-12-12 5.373-12 12-12 12 5.373 12 12Z" fill="url(#a)"></path>
                        <path d="m72.035 58.385-1.882 1.882v-1.22c0-4.32-3.514-7.835-7.834-7.835s-7.834 3.514-7.834 7.834h.006c0 .804.121 1.598.361 2.359l1.341-.423a6.426 6.426 0 0 1-.296-1.936h-.005a6.435 6.435 0 0 1 6.427-6.428 6.435 6.435 0 0 1 6.428 6.428v1.209l-1.893-1.889-.993.996 3.597 3.588 3.572-3.571-.995-.994ZM66.484 63.935l.913 1.07c.302-.258.587-.54.847-.841l-1.064-.92c-.213.247-.447.48-.696.691ZM56.713 62.181l-1.227.688c.391.698.886 1.33 1.47 1.88l.964-1.024a6.435 6.435 0 0 1-1.207-1.544ZM58.221 65.718c.684.42 1.424.732 2.201.925l.34-1.364a6.374 6.374 0 0 1-1.804-.759l-.737 1.198ZM62.056 65.464l-.055 1.405a8.1 8.1 0 0 0 1.452-.076l-.202-1.391a6.559 6.559 0 0 1-1.194.062ZM64.518 65.083l.48 1.321c.374-.136.74-.302 1.09-.494l-.678-1.232a6.42 6.42 0 0 1-.892.405Z" fill="#fff"></path>
                        <defs>
                            <lineargradient id="a" x1="51" y1="59" x2="75" y2="59" gradientunits="userSpaceOnUse">
                                <stop stop-color="#00F38D"></stop>
                                <stop offset="1" stop-color="#009EFF"></stop>
                            </lineargradient>
                        </defs>
                    </svg>
                </div>
                <form class="mt-8" method='POST' action="<?php echo $alamat_website . 'process_reset_password'; ?>">
                <div class="relative mt-4 lg:mt-5 rounded-xl group border bg-background-default border-transparent focus-within:border-primary focus-within:ring-1">
                        <div class="relative flex items-center top-0 pt-3 px-3 ">
                            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 13c0-1.886 0-2.828.586-3.414C5.172 9 6.114 9 8 9h8c1.886 0 2.828 0 3.414.586C20 10.172 20 11.114 20 13v2c0 2.828 0 4.243-.879 5.121C18.243 21 16.828 21 14 21h-4c-2.828 0-4.243 0-5.121-.879C4 19.243 4 17.828 4 15v-2Z" stroke="var(--primary)" stroke-width="2"></path>
                                <path d="M16 8V7a4 4 0 0 0-4-4v0a4 4 0 0 0-4 4v1" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
                                <circle cx="12" cy="15" r="2" fill="var(--primary)"></circle>
                            </svg><label class="text-xs pl-2 bg-background-default rounded-full ">Password</label>
                        </div>
                        <div class="relative">
                        <input type="password" name="old-password" placeholder="Enter old password" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" value="">
                        <span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]"><svg width="20" height="20" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="20">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.92 12.799a4 4 0 0 0-4.719-4.719l.923.923a3 3 0 0 1 2.873 2.873l.923.923Zm-6.527-2.285a3 3 0 0 0 4.093 4.093l.726.726a4 4 0 0 1-5.545-5.545l.726.726Z" fill="var(--base)"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="m16.154 17.275-.735-.734c-1.064.579-2.22.959-3.419.959-1.672 0-3.262-.74-4.633-1.726-1.367-.984-2.474-2.182-3.17-3.026-.423-.515-.467-.604-.467-.748 0-.143.044-.233.468-.748.67-.812 1.72-1.953 3.018-2.915L6.5 7.623C5.17 8.63 4.104 9.793 3.426 10.616l-.059.072c-.33.399-.637.77-.637 1.312s.307.913.637 1.312l.059.072c.725.88 1.894 2.149 3.357 3.201C8.243 17.635 10.036 18.5 12 18.5c1.51 0 2.92-.511 4.154-1.225ZM9.19 6.07c.88-.35 1.824-.569 2.81-.569 1.964 0 3.758.865 5.217 1.915 1.463 1.052 2.632 2.321 3.357 3.201l.059.072c.33.399.637.77.637 1.312s-.307.913-.637 1.312l-.059.072a19.988 19.988 0 0 1-1.983 2.086l-.708-.708a18.943 18.943 0 0 0 1.92-2.014c.424-.515.467-.604.467-.748 0-.143-.043-.233-.468-.748-.695-.844-1.802-2.042-3.17-3.026C15.263 7.24 13.673 6.5 12 6.5c-.694 0-1.375.128-2.031.348l-.78-.78Z" fill="var(--base)"></path>
                                    <path d="m5 2 16 16" stroke="var(--base)"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="relative mt-4 lg:mt-5 rounded-xl group border bg-background-default border-transparent focus-within:border-primary focus-within:ring-1">
                        <div class="relative flex items-center top-0 pt-3 px-3 ">
                            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 13c0-1.886 0-2.828.586-3.414C5.172 9 6.114 9 8 9h8c1.886 0 2.828 0 3.414.586C20 10.172 20 11.114 20 13v2c0 2.828 0 4.243-.879 5.121C18.243 21 16.828 21 14 21h-4c-2.828 0-4.243 0-5.121-.879C4 19.243 4 17.828 4 15v-2Z" stroke="var(--primary)" stroke-width="2"></path>
                                <path d="M16 8V7a4 4 0 0 0-4-4v0a4 4 0 0 0-4 4v1" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
                                <circle cx="12" cy="15" r="2" fill="var(--primary)"></circle>
                            </svg><label class="text-xs pl-2 bg-background-default rounded-full ">New Password</label>
                        </div>
                        <div class="relative">
                            <input type="password" name="new-password" label="new-password" placeholder="6 - 14 characters letters, numbers or symbols" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" value="">
                            <span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]">
                                <svg width="20" height="20" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="20">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.92 12.799a4 4 0 0 0-4.719-4.719l.923.923a3 3 0 0 1 2.873 2.873l.923.923Zm-6.527-2.285a3 3 0 0 0 4.093 4.093l.726.726a4 4 0 0 1-5.545-5.545l.726.726Z" fill="var(--base)"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="m16.154 17.275-.735-.734c-1.064.579-2.22.959-3.419.959-1.672 0-3.262-.74-4.633-1.726-1.367-.984-2.474-2.182-3.17-3.026-.423-.515-.467-.604-.467-.748 0-.143.044-.233.468-.748.67-.812 1.72-1.953 3.018-2.915L6.5 7.623C5.17 8.63 4.104 9.793 3.426 10.616l-.059.072c-.33.399-.637.77-.637 1.312s.307.913.637 1.312l.059.072c.725.88 1.894 2.149 3.357 3.201C8.243 17.635 10.036 18.5 12 18.5c1.51 0 2.92-.511 4.154-1.225ZM9.19 6.07c.88-.35 1.824-.569 2.81-.569 1.964 0 3.758.865 5.217 1.915 1.463 1.052 2.632 2.321 3.357 3.201l.059.072c.33.399.637.77.637 1.312s-.307.913-.637 1.312l-.059.072a19.988 19.988 0 0 1-1.983 2.086l-.708-.708a18.943 18.943 0 0 0 1.92-2.014c.424-.515.467-.604.467-.748 0-.143-.043-.233-.468-.748-.695-.844-1.802-2.042-3.17-3.026C15.263 7.24 13.673 6.5 12 6.5c-.694 0-1.375.128-2.031.348l-.78-.78Z" fill="var(--base)"></path>
                                    <path d="m5 2 16 16" stroke="var(--base)"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="relative mt-4 lg:mt-5 rounded-xl group border bg-background-default border-transparent focus-within:border-primary focus-within:ring-1">
                        <div class="relative flex items-center top-0 pt-3 px-3 ">
                            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 13c0-1.886 0-2.828.586-3.414C5.172 9 6.114 9 8 9h8c1.886 0 2.828 0 3.414.586C20 10.172 20 11.114 20 13v2c0 2.828 0 4.243-.879 5.121C18.243 21 16.828 21 14 21h-4c-2.828 0-4.243 0-5.121-.879C4 19.243 4 17.828 4 15v-2Z" stroke="var(--primary)" stroke-width="2"></path>
                                <path d="M16 8V7a4 4 0 0 0-4-4v0a4 4 0 0 0-4 4v1" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
                                <circle cx="12" cy="15" r="2" fill="var(--primary)"></circle>
                            </svg>
                            <label class="text-xs pl-2 bg-background-default rounded-full ">Confirm New Password</label>
                        </div>
                        <div class="relative">
                        <input type="password" name="cnew-password" label="new-password" placeholder="6 - 14 characters letters, numbers or symbols" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" value="">
                        <span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]"><svg width="20" height="20" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="20">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.92 12.799a4 4 0 0 0-4.719-4.719l.923.923a3 3 0 0 1 2.873 2.873l.923.923Zm-6.527-2.285a3 3 0 0 0 4.093 4.093l.726.726a4 4 0 0 1-5.545-5.545l.726.726Z" fill="var(--base)"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="m16.154 17.275-.735-.734c-1.064.579-2.22.959-3.419.959-1.672 0-3.262-.74-4.633-1.726-1.367-.984-2.474-2.182-3.17-3.026-.423-.515-.467-.604-.467-.748 0-.143.044-.233.468-.748.67-.812 1.72-1.953 3.018-2.915L6.5 7.623C5.17 8.63 4.104 9.793 3.426 10.616l-.059.072c-.33.399-.637.77-.637 1.312s.307.913.637 1.312l.059.072c.725.88 1.894 2.149 3.357 3.201C8.243 17.635 10.036 18.5 12 18.5c1.51 0 2.92-.511 4.154-1.225ZM9.19 6.07c.88-.35 1.824-.569 2.81-.569 1.964 0 3.758.865 5.217 1.915 1.463 1.052 2.632 2.321 3.357 3.201l.059.072c.33.399.637.77.637 1.312s-.307.913-.637 1.312l-.059.072a19.988 19.988 0 0 1-1.983 2.086l-.708-.708a18.943 18.943 0 0 0 1.92-2.014c.424-.515.467-.604.467-.748 0-.143-.043-.233-.468-.748-.695-.844-1.802-2.042-3.17-3.026C15.263 7.24 13.673 6.5 12 6.5c-.694 0-1.375.128-2.031.348l-.78-.78Z" fill="var(--base)"></path>
                                    <path d="m5 2 16 16" stroke="var(--base)"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-center my-5">
                    <button type="submit"  name="chg_pass" aria-label="Save password button" aria-labelledby="Save password button" class="bg-primary text-white lg:hover:brightness-95 rounded-xl text-sm lg:text-base font-semibold w-3/4 lg:w-1/2 justify-center py-3">Save</button>
                    </div>
                </form>
            </div>
        </section>
        <!--/$-->
    </div>
</section>
<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        const oldPassword = document.querySelector('input[name="old-password"]').value;
        const newPassword = document.querySelector('input[name="new-password"]').value;
        const confirmPassword = document.querySelector('input[name="confirm-password"]').value;

        if (!oldPassword || !newPassword || !confirmPassword) {
            alert('Semua kolom harus diisi!');
            event.preventDefault();
            return;
        }

        if (newPassword !== confirmPassword) {
            alert('Kata sandi baru dan konfirmasi kata sandi tidak cocok!');
            event.preventDefault();
            return;
        }

        if (newPassword.length < 6 || newPassword.length > 14) {
            alert('Kata sandi baru harus memiliki 6 hingga 14 karakter.');
            event.preventDefault();
            return;
        }
    });
</script>
