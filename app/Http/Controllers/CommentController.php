<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function save(Request $request){
        
        //Validación
        $validate = $this->validate($request, [
            'image_id' => 'integer|required',
            'content' => 'string|required'
        ]);
        //Recoger datos
        $user = \Auth::user();
        $image_id = $request->input('image_id');
        $content = $request->input('content');

        //Asigno los valores a mi nuevo objeto
        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->image_id = $image_id;
        $comment->content = $content;

        //Guardar en la base de datos
        $comment->save();

        //Redireccion
        return redirect()->route('image.detail', ['id'=> $image_id])->with([
                            'message' => 'Tu comentario se ha publicado!'
                            ]);
    }

    public function delete($id){
        //Conseguir datos de usuario identificado
        $user = \Auth::user();

        //Conseguir datos y objeto del comentario
        $comment = Comment::find($id);

        //Comprobar si soy el dueño del comentario o de la publicación
        if($user && ($comment->user_id == $user->id || $comment->image->id == $user->id)){
            $comment->delete();
            return redirect()->route('image.detail', ['id'=> $comment->image->id])->with([
                            'message' => 'Comentario eliminado!'
            ]);
        }else{
            return redirect()->route('image.detail', ['id'=> $comment->image->id])->with([
                            'message' => 'El comentario no se ha eliminado!'
            ]);
        }
    }
}
