@section('title', 'Create a product | HopeXito')
<x-app-layout>
    <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data"
        x-data="productStudio({ catalog: @js($catalog), geometry: @js($geometry), initialType: @js($initialType), initialPreviewColor: @js($initialPreviewColor), initialTitle: @js(old('title', '')), initialTags: @js(old('tags', '')), assetBase: @js($assetBase) })"
        x-ref="form" x-on:submit="submitForm" class="product-studio" data-product-studio>
        @csrf

        <input type="hidden" name="product_type" :value="productType">
        <input type="hidden" name="preview_color" :value="previewColor">
        <input type="hidden" name="transforms[front][x]" :value="transforms.front.x">
        <input type="hidden" name="transforms[front][y]" :value="transforms.front.y">
        <input type="hidden" name="transforms[front][scale]" :value="transforms.front.scale">
        <input type="hidden" name="transforms[front][rotation]" :value="transforms.front.rotation">
        <input type="hidden" name="transforms[back][x]" :value="transforms.back.x">
        <input type="hidden" name="transforms[back][y]" :value="transforms.back.y">
        <input type="hidden" name="transforms[back][scale]" :value="transforms.back.scale">
        <input type="hidden" name="transforms[back][rotation]" :value="transforms.back.rotation">

        <header class="studio-header">
            <a href="{{ route('product.manage') }}" class="studio-back-link" aria-label="Back to products">
                <span aria-hidden="true">←</span>
                <span>Products</span>
            </a>
            <div class="studio-header-meta">
                <span class="studio-kicker">Product studio</span>
                <span class="studio-header-dot" aria-hidden="true"></span>
                <span x-text="typeConfig.label"></span>
            </div>
            <div class="studio-header-status">
                <span class="studio-status-dot" :class="{ 'is-ready': canPublish }" aria-hidden="true"></span>
                <span x-text="canPublish ? 'Ready to publish' : 'Draft in progress'"></span>
            </div>
        </header>

        <div class="studio-layout">
            <main class="studio-canvas-region" aria-label="Product canvas">
                <div class="studio-canvas-toolbar">
                    <div>
                        <p class="studio-canvas-eyebrow">Live garment preview</p>
                        <p class="studio-canvas-title"><span x-text="typeConfig.label"></span> · <span x-text="previewColor"></span></p>
                    </div>
                    <div class="studio-side-controls" role="group" aria-label="Choose garment side">
                        <button type="button" class="studio-side-tab" :class="{ 'is-active': activeSide === 'front' }"
                            x-on:click="setSide('front')">Front</button>
                        <button type="button" class="studio-side-tab" :class="{ 'is-active': activeSide === 'back' }"
                            x-on:click="setSide('back')">Back</button>
                        <button type="button" class="studio-flip-button" x-on:click="flipSide" aria-label="Flip to the other side"
                            title="Flip garment">
                            <span aria-hidden="true">↻</span>
                        </button>
                    </div>
                </div>

                <div class="studio-viewport" x-ref="viewport" x-on:wheel.prevent="zoomViewport($event)"
                    x-on:pointerdown="startViewportPan($event)">
                    <div class="studio-grid" aria-hidden="true"></div>
                    <div class="studio-canvas-note studio-canvas-note-top">850 × 900 mockup space</div>
                    <div class="studio-canvas-note studio-canvas-note-bottom" x-text="activeSide === 'front' ? 'Front view' : 'Back view'"></div>

                    <div class="studio-stage" x-ref="stage" :class="{ 'is-flipping': isFlipping }" :style="stageStyle">
                        <img class="studio-garment" :src="mockupUrl" :alt="`${typeConfig.label} ${previewColor} ${activeSide} mockup`">
                        <div class="studio-print-area" x-ref="printArea" :style="printAreaStyle">
                            <template x-if="hasArtwork">
                                <div class="studio-artwork" tabindex="0" :class="{ 'is-selected': artworkSelected }"
                                    :style="artworkStyle" x-on:pointerdown.stop="startArtworkInteraction($event, 'drag')"
                                    x-on:keydown="nudgeArtwork($event)" x-on:focus="artworkSelected = true">
                                    <img :src="artworkUrl" :alt="`${activeSide} artwork preview`">
                                    <button type="button" class="studio-handle studio-handle-nw" aria-label="Resize artwork"
                                        x-on:pointerdown.stop="startArtworkInteraction($event, 'resize', 'nw')"></button>
                                    <button type="button" class="studio-handle studio-handle-ne" aria-label="Resize artwork"
                                        x-on:pointerdown.stop="startArtworkInteraction($event, 'resize', 'ne')"></button>
                                    <button type="button" class="studio-handle studio-handle-sw" aria-label="Resize artwork"
                                        x-on:pointerdown.stop="startArtworkInteraction($event, 'resize', 'sw')"></button>
                                    <button type="button" class="studio-handle studio-handle-se" aria-label="Resize artwork"
                                        x-on:pointerdown.stop="startArtworkInteraction($event, 'resize', 'se')"></button>
                                    <button type="button" class="studio-rotate-handle" aria-label="Rotate artwork"
                                        x-on:pointerdown.stop="startArtworkInteraction($event, 'rotate')"><span aria-hidden="true">↻</span></button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="studio-viewport-controls" role="group" aria-label="Canvas controls">
                    <button type="button" x-on:click="resetViewport" title="Fit canvas">Fit</button>
                    <button type="button" x-on:click="zoomOut" aria-label="Zoom out">−</button>
                    <span class="studio-zoom-readout" x-text="`${Math.round(viewport.zoom * 100)}%`"></span>
                    <button type="button" x-on:click="zoomIn" aria-label="Zoom in">+</button>
                    <span class="studio-viewport-hint">Scroll to zoom · Space + drag to pan</span>
                </div>
            </main>

            <aside class="studio-panel" x-ref="sheet" :class="{ 'is-open': sheetOpen, 'is-dragging': sheetDragging }"
                :style="sheetStyle" aria-label="Product controls"
                x-on:pointermove.window="moveSheetDrag($event)" x-on:pointerup.window="endSheetDrag($event)"
                x-on:pointercancel.window="endSheetDrag($event)">
                <div class="studio-sheet-drag-region" x-on:pointerdown="startSheetDrag($event)">
                    <button type="button" class="studio-sheet-toggle" :aria-expanded="sheetOpen"
                        :aria-label="sheetOpen ? 'Collapse studio controls' : 'Expand studio controls'"
                        x-on:click.stop="toggleSheet">
                        <span class="studio-sheet-grabber" aria-hidden="true"></span>
                    </button>
                    <div class="studio-panel-heading">
                        <div>
                            <p class="studio-kicker">Create a listing</p>
                            <h1>Make it yours.</h1>
                        </div>
                        <span class="studio-panel-price" x-text="`RM${Number(typeConfig.price).toFixed(2)}`"></span>
                    </div>
                </div>

                <nav class="studio-tabs" role="tablist" aria-label="Product creation steps">
                    <button type="button" role="tab" :aria-selected="activeTab === 'design'" :class="{ 'is-active': activeTab === 'design' }"
                        x-on:click="setTab('design')"><span>01</span>Design</button>
                    <button type="button" role="tab" :aria-selected="activeTab === 'product'" :class="{ 'is-active': activeTab === 'product' }"
                        x-on:click="setTab('product')"><span>02</span>Product</button>
                    <button type="button" role="tab" :aria-selected="activeTab === 'publish'" :class="{ 'is-active': activeTab === 'publish' }"
                        x-on:click="setTab('publish')"><span>03</span>Publish</button>
                </nav>

                <div class="studio-panel-scroll">
                    <section x-show="activeTab === 'design'" role="tabpanel" x-cloak>
                        <div class="studio-section-intro">
                            <p class="studio-section-label">01 / Build the product</p>
                            <p>Choose a garment, then shape the front or back design.</p>
                        </div>

                        <div class="studio-type-grid" role="radiogroup" aria-label="Product type">
                            <template x-for="(type, key) in catalog" :key="key">
                                <button type="button" role="radio" :aria-checked="productType === key" class="studio-type-option" :class="{ 'is-active': productType === key }" x-on:click="setProductType(key)">
                                    <span x-text="type.label"></span><strong x-text="`RM${Number(type.price).toFixed(0)}`"></strong>
                                </button>
                            </template>
                        </div>

                        <div class="studio-section-intro studio-section-intro-compact">
                            <p class="studio-section-label">Artwork</p>
                            <p>Set the artwork for the side currently on the canvas.</p>
                        </div>

                        <input x-ref="frontFile" id="front-design" name="image_front" type="file" accept="image/*"
                            class="sr-only" x-on:change="chooseFile('front', $event)">
                        <input x-ref="backFile" id="back-design" name="image_back" type="file" accept="image/*"
                            class="sr-only" x-on:change="chooseFile('back', $event)">

                        <div class="studio-upload-card" :class="{ 'has-file': files[activeSide] }">
                            <div class="studio-upload-icon" aria-hidden="true" x-text="files[activeSide] ? '✓' : '+'"></div>
                            <div class="min-w-0">
                                <p class="studio-upload-title" x-text="files[activeSide] ? files[activeSide].name : `${activeSide === 'front' ? 'Front' : 'Back'} artwork`"></p>
                                <p class="studio-upload-copy" x-text="files[activeSide] ? `${Math.round(files[activeSide].size / 1024)} KB · ready` : (activeSide === 'front' ? 'PNG, JPG, or WebP · max 8 MB' : 'Optional · upload artwork for this side')"></p>
                            </div>
                            <button type="button" class="studio-text-button" x-on:click="openFilePicker" x-text="files[activeSide] ? 'Replace' : 'Upload'"></button>
                        </div>
                        <button type="button" x-show="files[activeSide]" x-on:click="removeArtwork" class="studio-remove-button" x-cloak>Remove <span x-text="activeSide"></span> artwork</button>
                        <p class="studio-control-hint studio-delete-hint" x-show="files[activeSide]" x-cloak>Tip: select the artwork on canvas and press Delete to remove it.</p>
                        @error('image_front')<p class="studio-error">{{ $message }}</p>@enderror
                        @error('image_back')<p class="studio-error">{{ $message }}</p>@enderror
                        <p x-show="fileError" x-text="fileError" class="studio-error" x-cloak></p>

                        <div class="studio-control-block studio-color-control">
                            <div class="studio-control-heading"><span>Preview color</span><span class="studio-mono" x-text="`${typeColors.length} variants included`"></span></div>
                            <div class="studio-color-row" role="radiogroup" aria-label="Preview color">
                                <template x-for="color in typeColors" :key="color">
                                    <button type="button" role="radio" :aria-checked="previewColor === color" class="studio-color-swatch"
                                        :class="`color-${color.toLowerCase()} ${previewColor === color ? 'is-active' : ''}`" :title="color"
                                        x-on:click="previewColor = color; markDirty()"><span x-text="color"></span></button>
                                </template>
                            </div>
                        </div>

                        <div class="studio-control-block" x-show="hasArtwork" x-cloak>
                            <div class="studio-control-heading"><span>Transform <em x-text="activeSide"></em></span><button type="button" class="studio-reset-button" x-on:click="resetArtwork">Reset</button></div>
                            <label class="studio-range-label"><span>Scale</span><span class="studio-mono" x-text="`${Math.round(activeTransform.scale * 100)}%`"></span></label>
                            <input class="studio-range" type="range" min="0.25" max="2" step="0.01" x-model.number="transforms[activeSide].scale" x-on:input="markDirty">
                            <label class="studio-range-label"><span>Rotation</span><span class="studio-mono" x-text="`${Math.round(activeTransform.rotation)}°`"></span></label>
                            <input class="studio-range" type="range" min="-180" max="180" step="1" x-model.number="transforms[activeSide].rotation" x-on:input="markDirty">
                            <p class="studio-control-hint">Drag artwork to move it. Use the corners to resize.</p>
                        </div>
                    </section>

                    <section x-show="activeTab === 'product'" role="tabpanel" x-cloak>
                        <div class="studio-section-intro"><p class="studio-section-label">02 / Name the product</p><p>Add the details customers will see on the listing.</p></div>
                        <label class="studio-field-label" for="title">Title</label>
                        <input id="title" name="title" type="text" class="studio-field" placeholder="Give this design a name" required x-model="title" x-on:input="markDirty">
                        @error('title')<p class="studio-error">{{ $message }}</p>@enderror
                        <label class="studio-field-label" for="tags">Tags</label>
                        <input id="tags" name="tags" type="text" class="studio-field" placeholder="e.g. night, graphic, minimal" required x-model="tags" x-on:input="markDirty">
                        @error('tags')<p class="studio-error">{{ $message }}</p>@enderror
                        <div class="studio-fixed-price"><span>Retail price</span><strong x-text="`RM${Number(typeConfig.price).toFixed(2)}`"></strong><small>Fixed by catalog</small></div>
                    </section>

                    <section x-show="activeTab === 'publish'" role="tabpanel" x-cloak>
                        <div class="studio-section-intro"><p class="studio-section-label">Publish settings</p><p>Decide where this product can be seen and choose its cover side.</p></div>
                        <label class="studio-field-label" for="visibility">Visibility</label>
                        <select id="visibility" name="visibility" class="studio-field" x-on:change="markDirty">
                            <option value="public">Public · visible in your profile and marketplace</option>
                            <option value="private">Private · visible only to you</option>
                        </select>
                        <div class="studio-control-block">
                            <div class="studio-control-heading"><span>Card preview</span><span class="studio-mono" x-text="previewSide + ' image'"></span></div>
                            <div class="studio-preview-options">
                                <label :class="{ 'is-disabled': !files.front }"><input type="radio" name="preview_side" value="front" x-model="previewSide" :disabled="!files.front">Front image</label>
                                <label :class="{ 'is-disabled': !files.back }"><input type="radio" name="preview_side" value="back" x-model="previewSide" :disabled="!files.back">Back image</label>
                            </div>
                            <p x-show="previewValidationMessage" x-text="previewValidationMessage" class="studio-error" x-cloak></p>
                            @error('preview_side')<p class="studio-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="studio-commission-note"><span class="studio-note-mark">%</span><div><strong>15% creator commission</strong><p>You earn 15% of the fixed product price on external purchases. Your own purchases receive the owner discount and create no earnings.</p></div></div>
                        <label class="studio-rights-check"><input type="checkbox" name="rights" value="1" x-model="rightsAccepted" required><span>I have the right to sell products containing this artwork.</span></label>
                        @error('rights')<p class="studio-error">{{ $message }}</p>@enderror
                    </section>
                </div>

                <footer class="studio-panel-footer">
                    <button type="button" class="studio-footer-back" x-show="activeTab !== 'design'" x-on:click="previousTab" x-cloak>Back</button>
                    <span class="studio-footer-step" x-text="`${tabIndex + 1} / 3`"></span>
                    <button type="button" class="studio-footer-next" x-show="activeTab !== 'publish'" x-on:click="nextTab">Continue <span aria-hidden="true">→</span></button>
                    <button type="submit" class="studio-create-button" x-show="activeTab === 'publish'" :disabled="!canPublish || submitting" x-cloak>
                        <span x-show="!submitting">Create product <span aria-hidden="true">↗</span></span>
                        <span x-show="submitting">Generating product mockups…</span>
                    </button>
                </footer>
            </aside>
        </div>
    </form>
</x-app-layout>
