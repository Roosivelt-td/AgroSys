<div class="h-[calc(100vh-140px)] flex gap-4 animate-in fade-in duration-700"
     x-data="{
        viewerOpen: false,
        currentMedia: null,
        mediaList: @entangle('mediaList'),
        currentIndex: 0,
        openViewer(mediaId) {
            console.log('Abriendo visor para ID:', mediaId);
            let list = Array.from(this.mediaList);
            console.log('Lista de medios:', list);
            this.currentIndex = list.findIndex(m => m.id == mediaId);
            if(this.currentIndex !== -1) {
                this.currentMedia = list[this.currentIndex];
                this.viewerOpen = true;
            } else {
                console.error('No se encontró el medio en la lista');
            }
        },
        nextMedia() {
            let list = Array.from(this.mediaList);
            if (this.currentIndex < list.length - 1) {
                this.currentIndex++;
                this.currentMedia = list[this.currentIndex];
            }
        },
        prevMedia() {
            let list = Array.from(this.mediaList);
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.currentMedia = list[this.currentIndex];
            }
        }
     }">

    <!-- Lado Izquierdo: Lista de Conversaciones -->
    <div class="w-80 flex flex-col bg-agri-l_sidebar dark:bg-agri-d_sidebar rounded-2xl shadow-xl border border-slate-100 dark:border-white/5 overflow-hidden">

        <!-- Header de Contactos -->
        <div class="bg-agri-l_card dark:bg-agri-d_sidebar p-5 border-b border-agri-green/10">
            <h3 class="text-base font-black text-slate-800 dark:text-white italic tracking-tighter">Mensajería Técnica</h3>
            <div class="mt-4 relative group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar compañero o DNI..."
                       class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-900 border-none rounded-xl text-[10px] font-black uppercase tracking-widest outline-none shadow-inner">
            </div>
        </div>

        <!-- Lista de Conversaciones -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
            @if(strlen($search) > 1)
                <p class="px-4 py-2 text-[9px] font-black text-agri-green uppercase tracking-widest italic">Resultados de búsqueda</p>
                @forelse($contactos as $con)
                    <div wire:click="selectContact({{ $con->id }})"
                         class="flex items-center space-x-3 p-3 rounded-xl cursor-pointer transition-all border border-transparent hover:bg-agri-green/5">
                        <div class="w-10 h-10 rounded-xl overflow-hidden border border-agri-green/20 shrink-0">
                            <img src="{{ $con->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($con->nombres).'&background=00ba2e&color=fff' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-slate-800 dark:text-white truncate italic">{{ $con->nombres }}</p>
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ $con->display_role }}</p>
                        </div>
                    </div>
                @empty
                    <p class="p-4 text-center text-[10px] text-slate-400 italic">No se encontraron usuarios</p>
                @endforelse
                <div class="h-px bg-slate-100 dark:bg-white/5 my-2"></div>
            @endif

            <p class="px-4 py-2 text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Mis Canales y Chats</p>

            <!-- ACCESO RÁPIDO A AGROBOT -->
            <div wire:click="selectContact(0)"
                 class="flex items-center space-x-3 p-3 rounded-xl cursor-pointer transition-all border border-dashed border-agri-green/30 bg-agri-green/5 hover:bg-agri-green/10 mb-2 mx-2">
                <div class="w-10 h-10 rounded-xl bg-agri-green flex items-center justify-center text-white shadow-lg shrink-0">
                    <i class="fa-solid fa-robot text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black text-agri-green uppercase tracking-tighter leading-none">Consultar a AgroBot</p>
                    <p class="text-[8px] text-slate-500 dark:text-slate-400 italic">IA Asistente Técnico</p>
                </div>
            </div>

            @forelse($conversaciones as $conv)
                @php
                    $esGrupo = $conv->tipo_conversacion === 'grupal';
                    $esIndividual = $conv->tipo_conversacion === 'individual';
                    $otro = ($esGrupo || $esIndividual) ? null : $conv->participantes->where('usuario_id', '!=', auth()->id())->first()?->usuario;
                    $ultimoMsg = $conv->ultimo_mensaje_visible;
                    $noLeidos = $conv->conteo_no_leidos;
                @endphp
                <div wire:click="selectConversacion({{ $conv->id }})"
                     class="flex items-center space-x-3 p-3 rounded-xl cursor-pointer transition-all border border-transparent
                     {{ $selectedConversacionId == $conv->id ? 'bg-agri-l_card dark:bg-agri-green/10 border-agri-green/20' : 'hover:bg-white dark:hover:bg-white/5' }}">

                    <div class="relative shrink-0">
                        <div class="w-12 h-12 rounded-xl overflow-hidden border-2 {{ $selectedConversacionId == $conv->id ? 'border-agri-green' : 'border-slate-100 dark:border-white/10' }}">
                            @if($esGrupo)
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-agri-green">
                                    <i class="fa-solid fa-building-wheat text-xl"></i>
                                </div>
                            @elseif($esIndividual)
                                <div class="w-full h-full bg-agri-green/10 flex items-center justify-center text-agri-green">
                                    <i class="fa-solid fa-bookmark text-xl"></i>
                                </div>
                            @else
                                <img src="{{ $otro->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($otro->nombres ?? 'U').'&background=00ba2e&color=fff' }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if(!$esGrupo && !$esIndividual && $otro && $otro->is_activo)
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-agri-d_sidebar rounded-full shadow-sm"></div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <p class="text-xs font-black text-slate-800 dark:text-white truncate italic uppercase tracking-tighter">
                                @if($esGrupo) {{ $conv->nombre_grupo }}
                                @elseif($esIndividual) Mis Notas
                                @else {{ $otro->nombres ?? 'Compañero' }}
                                @endif
                            </p>
                            @if($ultimoMsg)
                                <span class="text-[7px] text-slate-400 font-bold uppercase">{{ $ultimoMsg->created_at->format('H:i') }}</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center mt-0.5">
                            <p class="text-[9px] text-slate-500 dark:text-slate-400 truncate flex-1 {{ $noLeidos > 0 ? 'font-black text-slate-900 dark:text-white' : '' }}">
                                {{ $ultimoMsg ? ($ultimoMsg->remitente_usuario_id == auth()->id() && !$esIndividual ? 'Tú: ' : ($esGrupo ? $ultimoMsg->remitente->nombres . ': ' : '')) . $ultimoMsg->mensaje : 'Inicia una coordinación' }}
                            </p>
                            @if($noLeidos > 0)
                                <span class="ms-2 px-1.5 py-0.5 bg-agri-green text-white text-[8px] font-black rounded-full min-w-[18px] text-center shadow-sm">{{ $noLeidos }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center"><p class="text-[10px] text-slate-400 italic">Sin conversaciones activas</p></div>
            @endforelse
        </div>
    </div>

    <!-- Lado Derecho: Ventana de Chat -->
    <div class="flex-1 flex flex-col bg-white dark:bg-agri-d_bg rounded-2xl shadow-2xl border border-slate-100 dark:border-white/5 overflow-hidden">

        @if($selectedConversacionId)
            @php
                $convActual = \App\Models\Conversacion::find($selectedConversacionId);
                $esGrupoActual = $convActual->tipo_conversacion === 'grupal';
                $esIndividualActual = $convActual->tipo_conversacion === 'individual';
            @endphp
            <!-- Header del Chat -->
            <div class="bg-agri-l_card dark:bg-agri-d_sidebar px-8 py-4 flex items-center justify-between border-b border-slate-100 dark:border-white/5">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-lg overflow-hidden border-2 border-white shadow-sm">
                        @if($esGrupoActual)
                            <div class="w-full h-full bg-agri-green flex items-center justify-center text-white">
                                <i class="fa-solid fa-people-group"></i>
                            </div>
                        @elseif($esIndividualActual)
                            <div class="w-full h-full bg-agri-green flex items-center justify-center text-white">
                                <i class="fa-solid fa-bookmark"></i>
                            </div>
                        @else
                            <img src="{{ $otroParticipante->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($otroParticipante->nombres).'&background=00ba2e&color=fff' }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 dark:text-white italic leading-none">
                            @if($esGrupoActual) {{ $convActual->nombre_grupo }}
                            @elseif($esIndividualActual) Mis Notas (Espacio personal)
                            @else {{ $otroParticipante->nombres . ' ' . $otroParticipante->apellidos }}
                            @endif
                        </p>
                        <span class="text-[9px] font-black text-agri-green uppercase tracking-widest mt-1 inline-block">
                            @if($esGrupoActual) Canal de Organización
                            @elseif($esIndividualActual) Archivo Personal
                            @else {{ $otroParticipante->display_role }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="deleteChat"
                            wire:confirm="{{ $esGrupoActual ? '¿Vaciar todos los mensajes de este canal para ti? El canal seguirá visible.' : '¿Borrar este historial personal? El chat desaparecerá hasta recibir un mensaje nuevo.' }}"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white dark:bg-white/5 text-slate-400 hover:text-rose-500 transition-all shadow-sm"
                            title="{{ $esGrupoActual ? 'Vaciar Canal' : 'Eliminar Chat' }}">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                    <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-white dark:bg-white/5 text-slate-400 hover:text-agri-green transition-all shadow-sm">
                        <i class="fa-solid fa-phone-flip text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Área de Mensajes -->
            <div class="flex-1 bg-agri-l_bg dark:bg-agri-d_accent/20 overflow-y-auto p-6 space-y-4 custom-scrollbar" id="chat-container" wire:poll.5s>
                @foreach($mensajes as $msg)
                    @php $esMio = $msg->remitente_usuario_id == auth()->id(); @endphp
                    <div class="flex items-end space-x-3 {{ $esMio ? 'justify-end ml-auto' : '' }} max-w-[80%]">
                        @if(!$esMio && !$msg->eliminado_todos)
                            <div class="w-8 h-8 rounded-md overflow-hidden shrink-0 shadow-sm border border-white">
                                <img src="{{ $msg->remitente->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($msg->remitente->nombres).'&background=00ba2e&color=fff' }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="relative group" x-data="{ openMenu: false }">
                            @if(!$msg->eliminado_todos)
                                <button @click="openMenu = !openMenu" class="absolute {{ $esMio ? '-left-8' : '-right-8' }} top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all text-slate-400 hover:text-agri-green">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </button>
                                <div x-show="openMenu" @click.away="openMenu = false" x-cloak x-transition class="absolute {{ $esMio ? 'right-0' : 'left-0' }} bottom-full mb-2 w-40 bg-white dark:bg-slate-900 shadow-2xl rounded-xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden">
                                    <div class="p-1.5 space-y-0.5">
                                        @if($msg->archivo_adjunto_id)
                                            <button wire:click="download({{ $msg->archivo_adjunto_id }})" class="w-full text-left px-3 py-2 text-[10px] font-black uppercase text-agri-green hover:bg-agri-green/10 rounded-lg transition-all tracking-widest italic">
                                                <i class="fa-solid fa-cloud-arrow-down mr-2 opacity-40"></i> Descargar
                                            </button>
                                        @endif
                                        @if($esMio)
                                            <button wire:click="startEditing({{ $msg->id }})" @click="openMenu = false" class="w-full text-left px-3 py-2 text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-agri-green/10 hover:text-agri-green rounded-lg transition-all italic tracking-widest">
                                                <i class="fa-solid fa-pen-to-square mr-2 opacity-40"></i> Editar
                                            </button>
                                            <button wire:click="deleteForEveryone({{ $msg->id }})" @click="openMenu = false" class="w-full text-left px-3 py-2 text-[10px] font-black uppercase text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all italic tracking-widest">
                                                <i class="fa-solid fa-trash-can mr-2 opacity-40"></i> Eliminar todos
                                            </button>
                                        @endif
                                        <button wire:click="deleteForMe({{ $msg->id }})" @click="openMenu = false" class="w-full text-left px-3 py-2 text-[10px] font-black uppercase text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition-all italic tracking-widest">
                                            <i class="fa-solid fa-eye-slash mr-2 opacity-40"></i> Eliminar para mí
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="p-2 rounded-2xl shadow-sm border {{ $msg->eliminado_todos ? 'bg-slate-50 dark:bg-white/5 text-slate-400 italic' : ($esMio ? 'bg-agri-green text-white border-agri-green rounded-br-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-100 dark:border-white/5 rounded-bl-none') }}">
                                @if($msg->eliminado_todos)
                                    <div class="flex items-center space-x-2 px-1 py-0.5"><i class="fa-solid fa-ban text-[10px] opacity-40"></i><p class="text-[11px] font-bold">Mensaje eliminado</p></div>
                                @else
                                    @if($esGrupoActual && !$esMio)
                                        <p class="text-[8px] font-black text-agri-green uppercase tracking-widest mb-1 italic">{{ $msg->remitente->nombres }} ({{ $msg->remitente->display_role }})</p>
                                    @endif
                                    @if($msg->archivo_adjunto_id)
                                        @php $media = $msg->adjunto; @endphp
                                        @if(str_contains($media->tipo_mime, 'image'))
                                            <div class="mb-2 rounded-xl overflow-hidden border border-white/20 shadow-sm max-w-[280px] relative group/img">
                                                <img src="{{ Storage::url($media->ruta_completa) }}"
                                                     class="w-full h-auto object-cover cursor-zoom-in hover:scale-105 transition-transform duration-500"
                                                     @click="openViewer({{ $msg->id }})">
                                                <a href="{{ Storage::url($media->ruta_completa) }}" download class="absolute top-2 right-2 w-8 h-8 rounded-lg bg-black/40 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover/img:opacity-100 transition-opacity">
                                                    <i class="fa-solid fa-download text-[10px]"></i>
                                                </a>
                                            </div>
                                        @elseif(str_contains($media->tipo_mime, 'video'))
                                            <div class="mb-2 rounded-xl overflow-hidden border border-white/20 shadow-sm max-w-[280px] relative group/vid">
                                                <video class="w-full h-auto">
                                                    <source src="{{ Storage::url($media->ruta_completa) }}" type="{{ $media->tipo_mime }}">
                                                </video>
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover/vid:bg-black/40 transition-colors cursor-pointer" @click="openViewer({{ $msg->id }})">
                                                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                                                        <i class="fa-solid fa-play text-xl"></i>
                                                    </div>
                                                </div>
                                                <a href="{{ Storage::url($media->ruta_completa) }}" download class="absolute top-2 right-2 w-8 h-8 rounded-lg bg-black/40 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover/vid:opacity-100 transition-opacity">
                                                    <i class="fa-solid fa-download text-[10px]"></i>
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex items-center space-x-3 p-3 mb-2 bg-black/5 dark:bg-white/5 rounded-xl border border-transparent hover:border-agri-green/20 group/file">
                                                <div class="w-10 h-10 rounded-lg bg-white dark:bg-slate-900 flex items-center justify-center text-agri-green shadow-sm group-hover/file:scale-110 transition-transform">
                                                    <i class="fa-solid fa-file-lines text-xl"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-[10px] font-black text-slate-700 dark:text-slate-200 truncate max-w-[150px] uppercase tracking-tighter">{{ $media->nombre_original }}</p>
                                                    <p class="text-[8px] text-slate-400 font-bold uppercase">{{ number_format($media->tamano_bytes / 1024, 1) }} KB</p>
                                                </div>
                                                <a href="{{ Storage::url($media->ruta_completa) }}" download class="w-8 h-8 flex items-center justify-center rounded-lg bg-agri-green/10 text-agri-green hover:bg-agri-green hover:text-white transition-all">
                                                    <i class="fa-solid fa-download text-xs"></i>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                    <p class="text-sm font-medium leading-relaxed italic">{{ $msg->mensaje }}</p>
                                    <div class="flex items-center mt-1 space-x-1 {{ $esMio ? 'justify-end' : '' }}">
                                        @if($msg->editado_at) <span class="text-[8px] font-black uppercase opacity-60 mr-1 italic">Editado</span> @endif
                                        <span class="text-[11px] font-black uppercase opacity-50">{{ $msg->created_at->format('H:i') }}</span>
                                        @if($esMio) <i class="fa-solid fa-check-double text-[7px] {{ $msg->leido ? 'text-blue-300' : 'opacity-40' }}"></i> @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- INDICADOR IA ESCRIBIENDO -->
                @if($isAiThinking)
                <div class="flex items-end space-x-3 max-w-[80%] animate-pulse">
                    <div class="w-8 h-8 rounded-md bg-agri-green flex items-center justify-center text-white shrink-0 shadow-sm">
                        <i class="fa-solid fa-robot text-xs"></i>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-bl-none shadow-sm flex space-x-1.5">
                        <div class="w-1.5 h-1.5 bg-agri-green rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-agri-green rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                        <div class="w-1.5 h-1.5 bg-agri-green rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Input de Mensaje -->
            <div class="p-5 bg-white dark:bg-agri-d_sidebar border-t border-slate-100 dark:border-white/5">
                @if($esSoloLectura)
                    <div class="bg-slate-100 dark:bg-white/5 p-4 rounded-xl text-center border border-slate-200 dark:border-white/10">
                        <p class="text-xs font-black text-slate-500 uppercase tracking-widest italic">
                            <i class="fa-solid fa-shield-halved mr-2"></i> Este es un canal de solo lectura del Super Admin
                        </p>
                    </div>
                @else
                    <form wire:submit.prevent="sendMessage" class="space-y-3">
                        @if($editingMessageId)
                            <div class="flex items-center justify-between bg-agri-l_card dark:bg-white/5 p-3 rounded-xl border-l-4 border-agri-green">
                                <div class="flex items-center space-x-3">
                                    <div class="text-agri-green"><i class="fa-solid fa-pen text-sm"></i></div>
                                    <div><p class="text-[10px] font-black text-agri-green uppercase">Editando mensaje</p></div>
                                </div>
                                <button type="button" wire:click="cancelEditing" class="w-8 h-8 rounded-full hover:bg-slate-200 dark:hover:bg-white/10 transition-all"><i class="fa-solid fa-xmark text-slate-400"></i></button>
                            </div>
                        @endif
                        @if($file)
                            <div class="flex items-center justify-between bg-agri-l_card dark:bg-white/5 p-3 rounded-xl border border-dashed border-agri-green animate-in slide-in-from-bottom-2 duration-300">
                                <div class="flex items-center space-x-3"><div class="w-10 h-10 rounded-lg bg-agri-green flex items-center justify-center text-white shadow-lg"><i class="fa-solid fa-paperclip"></i></div><div class="min-w-0"><p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate max-w-[200px]">{{ $file->getClientOriginalName() }}</p></div></div>
                                <button type="button" wire:click="$set('file', null)" class="w-8 h-8 rounded-full bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        @endif
                        <div class="flex items-center space-x-3">
                            <label class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-agri-green transition-colors bg-slate-50 dark:bg-white/5 rounded-xl cursor-pointer">
                            <i class="fa-solid fa-paperclip text-lg"></i>
                            <input type="file" wire:model="file" class="hidden" x-ref="fileInput"
                                   @change="
                                    const file = $event.target.files[0];
                                    if (file && file.size > 100 * 1024 * 1024) {
                                        alert('¡Archivo demasiado grande! El tamaño máximo permitido es de 100MB.');
                                        $el.value = '';
                                    }
                                   ">
                        </label>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-900 rounded-xl px-5 py-2.5 border border-slate-100 dark:border-white/10 focus-within:ring-2 focus-within:ring-agri-green/20 transition-all">
                                <input wire:model="message" type="text" placeholder="Escribe una coordinación técnica..." class="w-full bg-transparent border-none focus:ring-0 text-sm placeholder:italic text-slate-700 dark:text-white py-1">
                            </div>
                            <button type="submit" class="w-12 h-12 bg-agri-green text-white rounded-xl flex items-center justify-center shadow-xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all"><i class="fa-solid fa-paper-plane"></i></button>
                        </div>
                    </form>
                @endif
            </div>

        @else
            <!-- Estado Vacío -->
            <div class="flex-1 flex flex-col items-center justify-center bg-agri-l_bg dark:bg-agri-d_accent/10 text-center p-20">
                <div class="w-28 h-28 bg-agri-l_card dark:bg-agri-d_sidebar rounded-[2.5rem] flex items-center justify-center text-agri-green mb-8 shadow-inner animate-bounce-slow">
                    <i class="fa-solid fa-comments text-4xl"></i>
                </div>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter">Mensajería AgroSys</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest max-w-xs mt-4">Canal seguro de comunicación técnica para organizaciones agrícolas.</p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            const chatContainer = document.getElementById('chat-container');
            if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
        });

        window.addEventListener('file-uploaded', () => {
            // Reset the file input value to allow re-uploading the same file if needed
            const input = document.querySelector('input[type="file"]');
            if(input) input.value = '';
        });

        window.addEventListener('scroll-chat', () => {
            setTimeout(() => {
                const chatContainer = document.getElementById('chat-container');
                if (chatContainer) chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
            }, 100);
        });

        // Capturar errores de subida de Livewire (Filtro de seguridad del servidor)
        window.addEventListener('livewire:upload-error', (event) => {
            alert('Error al subir el archivo: Es probable que sea demasiado grande para procesarlo o el servidor ha rechazado la conexión.');
        });
    </script>

    <!-- VISOR MULTIMEDIA PREMIUM (SLIDER) -->
    <div x-show="viewerOpen"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm px-4 md:px-20"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @keydown.escape.window="viewerOpen = false"
         @keydown.left.window="prevMedia()"
         @keydown.right.window="nextMedia()">

        <!-- Cerrar -->
        <button @click="viewerOpen = false" class="absolute top-6 right-6 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-all z-[110]">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- Descargar desde Visor -->
        <a :href="currentMedia ? currentMedia.url : '#'" download class="absolute top-6 right-20 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-agri-green transition-all z-[110]">
            <i class="fa-solid fa-download text-xl"></i>
        </a>

        <!-- Navegación Izquierda -->
        <button x-show="currentIndex > 0" @click="prevMedia()" class="absolute left-6 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-all z-[110]">
            <i class="fa-solid fa-chevron-left text-2xl"></i>
        </button>

        <!-- Navegación Derecha -->
        <button x-show="currentIndex < mediaList.length - 1" @click="nextMedia()" class="absolute right-6 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-all z-[110]">
            <i class="fa-solid fa-chevron-right text-2xl"></i>
        </button>

        <!-- Contenido -->
        <div class="relative w-full h-full flex items-center justify-center pointer-events-none">
            <template x-if="currentMedia && currentMedia.tipo === 'image'">
                <img :src="currentMedia.url" class="max-w-full max-h-[85vh] object-contain shadow-2xl pointer-events-auto rounded-lg">
            </template>
            <template x-if="currentMedia && currentMedia.tipo === 'video'">
                <video :src="currentMedia.url" controls autoplay class="max-w-full max-h-[85vh] shadow-2xl pointer-events-auto rounded-lg"></video>
            </template>
        </div>

        <!-- Info Inferior -->
        <div class="absolute bottom-10 text-center text-white/60 text-[10px] font-black uppercase tracking-[0.5em]">
            Archivo <span x-text="currentIndex + 1"></span> de <span x-text="mediaList.length"></span>
        </div>
    </div>
</div>
