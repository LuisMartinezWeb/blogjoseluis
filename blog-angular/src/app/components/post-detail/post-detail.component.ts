import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params} from '@angular/router';
import { Post } from "../../models/post";
import { PostService } from "../../services/post.service";
import { UserService } from "../../services/user.service";
import { User } from 'src/app/models/user';
import { global } from "../../services/global";

@Component({
  selector: 'app-post-detail',
  templateUrl: './post-detail.component.html',
  styleUrls: ['./post-detail.component.css'],
  providers: [PostService, UserService]
})
export class PostDetailComponent implements OnInit {
  public post: any;
  public url;
  public identity;
  constructor(
    private _postService: PostService,
    private _route: ActivatedRoute,
    private _router: Router,
    private _userService: UserService
  ){
    this.identity = this._userService.getIdentity();
    this.url = global.url;
   }

  ngOnInit(): void {
    this.getPost();
  }

  getPost(){
    //sacar el id del post de la url
    this._route.params.subscribe(params =>{
      let id = +params['id'];

      //peticion a ajax para sacar los datos

      this._postService.getPost(id).subscribe(
        response => {
          if(response.status == 'success'){
            this.post = response.post;
            console.log(this.post);
          }else{
            this._router.navigate(['/inicio']);
          }
        },
        error =>{
          console.log(error);
          this._router.navigate(['/inicio']);
        }
      );
    });

   


  }

}
