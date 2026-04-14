<div
    id="trackingModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 px-4"
>
    <div class="w-full max-w-[520px] bg-white rounded-[10px] shadow-2xl animate-[fadeIn_.25s_ease]">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <h2 class="text-[24px] font-bold text-green-700">
                Localiser mon trajet
            </h2>
            <button
                type="button"
                id="closeTrackingModal"
                class="text-gray-400 hover:text-green-700 text-[28px] leading-none font-bold"
            >
                ×
            </button>
        </div>

        <div class="px-6 py-5">
            <form action="geolocalisation/verifier-trajet.php" method="post" class="space-y-5">
                <div>
                    <label for="tracking_reservation_number" class="block mb-2 text-[14px] font-medium text-black">
                        Numéro de réservation
                    </label>
                    <input
                        type="text"
                        id="tracking_reservation_number"
                        name="Numero_reservation"
                        placeholder="Ex : 8I4P5SPD"
                        required
                        class="w-full h-[48px] rounded-[6px] border border-gray-300 px-4 text-[15px] outline-none focus:border-green-700"
                    >
                    <p class="mt-2 text-[13px] text-gray-500">
                        Entrez le numéro de réservation figurant sur votre billet.
                    </p>
                </div>

                <div>
                    <label for="tracking_email" class="block mb-2 text-[14px] font-medium text-black">
                        Adresse mail
                    </label>
                    <input
                        type="email"
                        id="tracking_email"
                        name="email"
                        placeholder="alex99@gmail.com"
                        class="w-full h-[48px] rounded-[6px] border border-gray-300 px-4 text-[15px] outline-none focus:border-green-700"
                    >
                </div>

                <div class="flex items-center justify-center">
                    <span class="text-[13px] text-gray-400 font-semibold">OU</span>
                </div>

                <div>
                    <label for="tracking_phone" class="block mb-2 text-[14px] font-medium text-black">
                        Numéro de téléphone
                    </label>
                    <input
                        type="text"
                        id="tracking_phone"
                        name="telephone"
                        placeholder="Ex : 690123456"
                        class="w-full h-[48px] rounded-[6px] border border-gray-300 px-4 text-[15px] outline-none focus:border-green-700"
                    >
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full h-[50px] rounded-[6px] bg-green-700 hover:bg-green-800 text-white text-[16px] font-bold transition duration-200"
                    >
                        Vérifier et suivre mon trajet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>