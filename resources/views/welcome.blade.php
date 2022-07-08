@extends('layouts.app')

@section('content')
<div id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Iniciar sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('auth.login')
            </div>
        </div>
    </div>
</div>
<div id="home" class="carousel slide h-100 pt-5" data-bs-ride="carousel" data-bs-interval="7000">
    <div class="container">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="row vh-100">
                    <div class="col-12 col-sm-12 col-md-6 d-none d-md-flex justify-content-center align-items-center my-auto">
                        <div class="d-none d-md-block">
                            <div class="h2 fw-bolder text-muted">A nuestros clientes</div>
                            <p class="fs-4 text-justify">
                                ¿No sabes que ordenar?, ¿deseas conocer el producto de sitios nuevos?, Customer Connection ofrece múltiples servicios al alcance de una llamada, mensaje y muchos otros medios de comunicación.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 d-md-flex justify-content-center align-items-center my-auto">
                        <img src="/images/2.png" class="col-12 slider-img" alt="">
                        <div class="d-md-none text-dark text-start">
                            <div class="h2 fw-bolder">A nuestros clientes</div>
                            <p class="fs-4 text-justify">
                                ¿No sabes que ordenar?, ¿deseas conocer el producto de sitios nuevos?, Customer Connection ofrece múltiples servicios al alcance de una llamada, mensaje y muchos otros medios de comunicación.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="row vh-100">
                    <div class="col-12 col-sm-12 col-md-6 d-none d-md-flex justify-content-center align-items-center my-auto">
                        <div class="d-none d-md-block">
                            <div class="h2 fw-bolder text-muted">A nuestros asociados</div>
                            <p class="fs-4 text-justify">
                                El éxito de una empresa se bifurca principalmente entre el producto y la atención. Enfócate en el primero que nosotros nos encargamos del segundo.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 d-md-flex justify-content-center align-items-center my-auto">
                        <img src="/images/1.svg" class="col-12 slider-img" alt="">
                        <div class="d-md-none text-dark text-start">
                            <div class="h2 fw-bolder">A nuestros asociados</div>
                            <p class="fs-4 text-justify">
                                El éxito de una empresa se bifurca principalmente entre el producto y la atención. Enfócate en el primero que nosotros nos encargamos del segundo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#home" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#home" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
</div>
<div id="about" class="pt-5">
    <div class="container">
        <div class="row vh-100">
            <div class="col-md-1 circle d-none d-md-block"></div>
            <div class="col-12 fs-2 title text-start pt-5"
                data-aos="flip-right"
                data-aos-offset="300"
                data-aos-delay="50"
                data-aos-duration="1000"
                data-aos-easing="ease-in-out"
                data-aos-once="true"
            >
                <span class="text-uppercase">Sobre</span> <span class="fw-bold text-uppercase">nosotros</span>
            </div>
            <div class="col-12 col-sm-12 col-md-5 fs-4 pt-2 text-muted"
                data-aos="fade"
                data-aos-delay="50"
                data-aos-duration="2000"
                data-aos-easing="ease-in-out"
                data-aos-once="true"
            >
                <p class="fs-4 text-justify">Somos una empresa con profesionales dedicados a generar experiencias memorables para nuestros asociados y clientes.</p>
                <p class="fs-4">Director ejecutivo (CEO):</p>
                <p class="fs-5 fst-italic fw-bold text-end">
                    Cesar Adolfo Barrios Castañeda<br>
                    (Profesional en Negocios internacionales)
                </p>
            </div>
            <div class="col-12 col-sm-12 col-md-7 d-flex justify-content-center">
                <div class="about-img"
                    data-aos="fade-right"
                    data-aos-delay="50"
                    data-aos-duration="1000"
                    data-aos-easing="ease-in-out"
                    data-aos-once="true"
                >
                    <img src="/images/develop.svg" height="360" alt="">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="services" class="pt-5">
    <div class="container pt-3">
        <div class="row">
            <div class="col-12 fs-2 title text-start pt-5"
                data-aos="flip-right"
                data-aos-offset="300"
                data-aos-delay="50"
                data-aos-duration="1000"
                data-aos-easing="ease-in-out"
                data-aos-once="true"
            >
                <span class="text-uppercase">servicios a</span> <span class="fw-bold text-uppercase">nuestros clientes</span>
            </div>
            <div class="col-12">
                <div class="row d-flex justify-content-center">
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="fade"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-cart-arrow-down icon"></i>
                            <h4 class="heading fw-bolder">Productos</h4>
                            <div class="description fs-5 text-justify">
                                Explora y disfruta de nuestra gran variedad de restaurantes, hoteles, licorerías, droguerías etc.  Al alcance de un mensaje, una llamada o cualquier otro medio.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="fade"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-key icon"></i>
                            <h4 class="heading fw-bolder">Reservaciones</h4>
                            <div class="description fs-5 text-justify">
                                Descubre lugares perfectos para festejar tus fechas especiales, escoge y reserva en hoteles o restaurantes a tu gusto desde la comodidad de tu casa.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 fs-2 title text-start pt-5"
                data-aos="flip-right"
                data-aos-offset="300"
                data-aos-delay="50"
                data-aos-duration="1000"
                data-aos-easing="ease-in-out"
                data-aos-once="true"
            >
                <span class="text-uppercase">servicios a</span> <span class="fw-bold text-uppercase">nuestros asociados</span>
            </div>
            <div class="col-12 pt-2">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="slide-left"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-phone icon"></i>
                            <h4 class="heading fw-bolder">Call center</h4>
                            <div class="description fs-5 text-justify">
                                Atención constante y profesional a cada cliente de nuestras empresas asociadas
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="fade"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-chart-line icon"></i>
                            <h4 class="heading fw-bolder">Base de datos</h4>
                            <div class="description fs-5 text-justify">
                                Se crean gráficas y tablas organizadas para llevar una correcta estadística de los clientes, ventas y productos.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="slide-right"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-motorcycle icon"></i>
                            <h4 class="heading fw-bolder">Domiciliarios</h4>
                            <div class="description fs-5 text-justify">
                                Por medio de filiales, se coordinan los domicilios para lograr un tiempo justo en cada pedido
                            </div>
                        </div>
                    </div>
                
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="fade"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-person-chalkboard icon"></i>
                            <h4 class="heading fw-bolder">Marketing enfocado</h4>
                            <div class="description fs-5 text-justify">
                                Con la información previamente organizada, se analiza para crear un portafolio de marketing enfocado en alcanzar y fidelizar a clientes potenciales.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4"
                        data-aos="fade"
                        data-aos-delay="100"
                        data-aos-duration="1000"
                        data-aos-easing="ease-in-out"
                        data-aos-once="true"
                    >
                        <div class="border services">
                            <i class="fa-solid fa-envelope-circle-check icon"></i>
                            <h4 class="heading fw-bolder">Telemercadeo</h4>
                            <div class="description fs-5 text-justify">
                                Para lanzamiento de eventos, promociones o productos nuevos, podemos hacer invitaciones formales a cada uno de sus clientes.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="contact" class="pt-5">
    <div class="container pt-3">
        <div class="row">
            <div class="fs-2 title text-start pt-4 mt-4">
                <span class="text-uppercase">Contáctanos</span>
            </div>
            <form id="contacto-form" action="" class="col-12 col-sm-12 col-md-6 py-4">
                <div class="alert alert-success alert-dismissible" role="alert"></div>
                <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>
                @csrf
                <div class="d-flex rounded-pill border mb-3 bg-white">
                    <i class="fa-solid fa-at fs-4 p-3"></i>
                    <input type="email" class="form-control bg-transparent border-0" name="email" placeholder="correo" required>
                </div>
                <div class="d-flex rounded-pill border mb-3 bg-white">
                    <i class="fa-solid fa-user fs-4 p-3"></i>
                    <input type="text" class="form-control bg-transparent border-0" name="name" placeholder="Nombre" required>
                </div>
                <div class="d-flex rounded-pill border mb-3 bg-white">
                    <i class="fa-solid fa-font fs-4 p-3"></i>
                    <input type="text" class="form-control bg-transparent border-0" name="subject" placeholder="Asunto" required>
                </div>
                <div class="d-flex border rounded  mb-3 bg-white">
                    <i class="fa-solid fa-keyboard fs-4 p-3 my-auto"></i>
                    <textarea class="form-control bg-transparent border-0" name="message" placeholder="Mensaje" required rows="5" style="resize: none"></textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn bg-primary bg-gradient btn-lg rounded-pill text-white" type="submit">Enviar correo</button>
                </div>
            </form>
            <div class="col-12 col-sm-12 col-md-6 text-center" data-aos="fade-in" data-aos-duration="1000" data-aos-once="true">
                <img src="/images/phone.png" height="360" alt="">
            </div>
        </div>
    </div>
</div>
<div class="pt-5">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-6 d-flex justify-content-center align-items-center" style="min-height: 25vw; background-image: linear-gradient(195deg, var(--bs-info) 0%, var(--bs-primary) 100%);">
            <ul class="list-unstyled my-auto">
                <li class="mb-3">
                    <a href="https://www.google.com/maps/place/1%C2%B050'46.2%22N+76%C2%B003'24.2%22W/@1.8461775,-76.0588968,17z/data=!3m1!4b1!4m5!3m4!1s0x0:0x79ecd78c8b33f86f!8m2!3d1.8461775!4d-76.0567081?hl=es" target="_blank" class="d-flex align-items-center text-decoration-none">
                        <i class="fa-solid fa-location-dot text-white fs-3"></i>
                        <div class="fs-4 text-white ps-2">Avenida 3 # 10-51 Sur</div>
                    </a>
                </li>
                <li class="mb-3">
                    <a href="https://api.whatsapp.com/send/?phone=573214839455&text=Buen día, me interesa contratar sus servicios" target="_blank" class="d-flex align-items-center text-decoration-none">
                        <i class="fa-solid fa-mobile-button text-white fs-3"></i>
                        <div class="fs-4 text-white ps-2">3214839455</div>
                    </a>
                </li>
                <li>
                    <a href="mailto:customerconnection4.0@gmail.com" target="_blank" class="d-flex align-items-center text-decoration-none">
                        <i class="fa-solid fa-envelope text-white fs-3"></i>
                        <div class="fs-4 text-white ps-2">customerconnection4.0@gmail.com</div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-12 col-sm-12 col-md-6" style="height: 25vw;">
            <iframe class="md-height" width="100%" height="100%" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=es&amp;q=1.846177505922243,-76.05670809745789+(Customer%20Connection)&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" allowfullscreen=""></iframe>
        </div>
        <div class="col-12 text-center py-2">
            <div class="fs-6 fw-bolder">© {{date('Y')}} derechos reservados</div>
        </div>
    </div>
</div>
@endsection