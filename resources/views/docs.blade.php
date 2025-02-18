<x-layouts.base previewify="756" :previewify-data="[
    'overline' => $package->name . ' v' . $package->version_id,
    'title' => $page->title,
    'subtitle' => $package->description,
    'repository' => 'laravel-filament/filament',
]">
    <x-nav />

    <div class="space-y-24">
        <div class="max-w-7xl mx-auto grid grid-cols-1 gap-8 lg:grid-cols-4">
            <aside
                x-data="{}"
                x-cloak
                :aria-hidden="$store.sidebar.isOpen.toString()"
                :class="$store.sidebar.isOpen ? '-translate-x-0' : '-translate-x-full'"
                class="fixed w-full max-w-xs p-8 space-y-8 inset-y-0 left-0 z-10 overflow-y-auto transition-transform duration-500 ease-in-out transform bg-gray-100 lg:w-auto lg:max-w-full lg:ml-8 lg:p-0 lg:-translate-x-0 lg:bg-transparent lg:relative lg:overflow-visible"
            >
                <div
                    x-data="{ package: '{{ $package->slug }}', version: '{{ $version->slug }}' }"
                    x-init="
                        $watch('package', () => window.location = `/docs/{{ $version->slug }}/${package}`)
                        $watch('version', () => window.location = `/docs/${version}/{{ $package->slug }}`)
                    "
                    class="space-y-2 -mx-3"
                >
                    <select x-model="package" class="block w-full h-10 border-gray-300 transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500">
                        @foreach (\App\Models\DocumentationPackage::orderBy('name')->get()->unique('slug') as $documentationPackage)
                            <option value="{{ $documentationPackage->slug }}">
                                {{ $documentationPackage->name }}
                            </option>
                        @endforeach
                    </select>

                    <select x-model="version" class="block w-full h-10 border-gray-300 transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500">
                        @foreach ($package->getVersions() as $packageVersion)
                            <option value="{{ $packageVersion->slug }}">
                                {{ $packageVersion->name }}
                            </option>
                        @endforeach
                    </select>

                    <div id="docsearch" class="border rounded-lg border-gray-300 shadow-sm"></div>
                </div>

                <ul class="space-y-4">
                    @foreach ($package->getPages() as $packagePage)
                        @php
                            $isActive = request()->route()->parameter('pageSlug') === $packagePage->slug;
                            $url = $packagePage->getUrl();
                        @endphp

                        <li class="space-y-1">
                            <a
                                href="{{ $url }}"
                                @class([
                                    'font-medium uppercase tracking-wider hover:text-primary-500 focus:text-primary-500',
                                    'text-gray-900' => ! $isActive,
                                    'text-primary-500' => $isActive,
                                ])
                            >
                                {{ $packagePage->title }}
                            </a>

                            <ul class="pl-4 border-l-2 space-y-2">
                                @foreach ($packagePage->getSections() as $slug => $heading)
                                    <li class="leading-5">
                                        <a
                                            href="{{ $url }}#{{ $slug }}"
                                            class="text-sm text-gray-700 hover:text-primary-500 focus:text-primary-500"
                                        >
                                            {{ $heading }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <main class="p-8 space-y-16 overflow-x-auto lg:py-0 lg:col-span-3">
                <div class="prose">
                    <h1 class="font-heading">
                        {{ $page->title }}
                    </h1>

                    @markdown($page->content)
                </div>

                <p class="text-lg">
                    Still need help? Join our <a href="{{ route('discord') }}" target="_blank" class="transition hover:text-primary-500">Discord community</a> or open a <a href="https://github.com/laravel-filament/filament/discussions/new" target="_blank" class="transition hover:text-primary-500">GitHub discussion</a>
                </p>
            </main>
        </div>

        <section class="bg-pink-500 flex justify-center">
            <div class="relative max-w-7xl w-full flex justify-center text-center mx-auto px-8 py-16">
                <div class="max-w-2xl space-y-8">
                    <div class="space-y-4">
                        <h2 class="font-heading font-bold text-primary-200 text-4xl">
                            Enjoying Filament?
                        </h2>

                        <p class="text-xl text-white">
                            We are open source at heart. To allow us to <strong>build new features</strong>, <strong>fix bugs</strong>, and <strong>run the community</strong>, we require your financial support.
                        </p>
                    </div>

                    <a
                        href="https://github.com/sponsors/danharrin"
                        target="_blank"
                        class="group inline-flex items-center justify-center px-6 text-lg sm:text-xl font-semibold tracking-tight text-white transition rounded-lg h-11 ring-2 ring-inset ring-white hover:bg-primary-200 hover:text-pink-500 hover:ring-primary-200 focus:ring-primary-200 focus:text-pink-500 focus:bg-primary-200 focus:outline-none"
                    >
                        Sponsor Filament on GitHub

                        <x-heroicon-o-heart class="ml-2 -mr-3 w-7 h-7 transition-all group-hover:scale-125" />
                    </a>
                </div>

                <div class="hidden absolute left-0 bottom-0 ml-12 -mb-2 xl:block">
                    <img
                        src="{{ asset('images/dragon.svg') }}"
                        alt="Dragon"
                        class="h-[16rem]"
                    />
                </div>

                <div class="hidden absolute right-0 top-0 mt-12 mr-12 xl:block">
                    <img
                        src="{{ asset('images/diamond.svg') }}"
                        alt="Diamond"
                        class="h-[8rem]"
                    />
                </div>
            </div>
        </section>
    </div>

    <x-footer />
</x-layouts.base>
