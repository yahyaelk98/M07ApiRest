<?php
namespace App\Controller;

use App\Repository\PeliculasRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PeliculasController
 * @package App\Controller
 *
 * @Route(path="/api/")
 */
class PeliculasController{
    private $peliculasRepository;

    public function __construct(PeliculasRepository $peliculasRepository){
        $this->peliculasRepository = $peliculasRepository;
    }

    /**
     * @Route("peliculas", name="add_pelicula", methods={"POST"})
     */
    public function add(Request $request) : JsonResponse{
        $data = json_decode($request->getContent(), true);

        $nombre = $data['nombre'];
        $genero = $data['genero'];
        $descripcion = $data['descripcion'];

        if(empty($nombre) || empty($genero) || empty($descripcion)){
            throw new NotFoundHttpException('Parametros obligatorios no encontrados.');
        }

        $this->peliculasRepository->savePelicula($nombre, $genero, $descripcion);

        return new JsonResponse(['status' => 'Pelicula creada!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("peliculas/{id}", name="get_pelicula", methods={"GET"})
     */
    public function get($id) : JsonResponse{
        $pelicula = $this->peliculasRepository->findOneBy(['id'=>$id]);

        $data = [
            'id' => $pelicula->getId(),
            'nombre' => $pelicula->getNombre(),
            'genero' => $pelicula->getGenero(),
            'descripcion' => $pelicula->getDescripcion()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("peliculas", name="get_peliculas", methods={"GET"})
     */
    public function getAll() : JsonResponse{
        $peliculas = $this->peliculasRepository->findAll();
        $data = [];

        foreach ($peliculas as $pelicula){
            $data[] = [
                'id' => $pelicula->getId(),
                'nombre' => $pelicula->getNombre(),
                'genero' => $pelicula->getGenero(),
                'descripcion' => $pelicula->getDescripcion()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("peliculas/{id}", name="update_pelicula", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse{
        $pelicula = $this->peliculasRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['nombre']) ? true : $pelicula->setNombre($data['nombre']);
        empty($data['genero']) ? true : $pelicula->setGenero($data['genero']);
        empty($data['descripcion']) ? true : $pelicula->setDescripcion($data['descripcion']);

        $this->peliculasRepository->updatePelicula($pelicula);

        return new JsonResponse(['status'=>'Pelicula actualizada!'], Response::HTTP_OK);
    }

    /**
     * @Route("peliculas/{id}", name="delete_pelicula", methods={"DELETE"})
     */
    public function delete($id): JsonResponse{
        $pelicula = $this->peliculasRepository->findOneBy(['id'=>$id]);

        $this->peliculasRepository->removePelicula($pelicula);

        return new JsonResponse(['status'=>'Pelicula eliminada!'], Response::HTTP_OK);
    }
}
