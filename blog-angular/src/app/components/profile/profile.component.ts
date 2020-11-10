import { Component, OnInit } from '@angular/core';
import { Post } from "../../models/post";
import { Router, ActivatedRoute, Params} from '@angular/router';
import { PostService } from "../../services/post.service";
import { UserService } from "../../services/user.service";
import { global } from "../../services/global";
import { User } from 'src/app/models/user';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers: [PostService, UserService]
})
export class ProfileComponent implements OnInit {

 
  public url;
  public posts: Array<Post>;
  public user:User;
  public identity;
  public token;
  constructor(
    private _postService: PostService,
    private _userService: UserService,
    private _route: ActivatedRoute,
    private _router: Router
  ) { 
    
    this.url = global.url;
    this.identity = _userService.getIdentity();
    this.token = _userService.getToken();
  }

  ngOnInit(): void {
    this.getProfile();
    
  }

  getPosts(userId){
    this._userService.getPosts(userId).subscribe(
      response => {
        if(response.status == 'success'){
          this.posts = response.posts;
          console.log(this.posts);
         
          
        }
      },
      error =>{
        console.log(error);
      }
    );
  }

  getProfile(){
    this._route.params.subscribe(params =>{
      let userId = +params['id'];
      this.getUser(userId);
      this.getPosts(userId);
    });
  }


  deletePost(id){
    this._postService.delete(this.token, id).subscribe(
      response =>{
        this.getProfile();
      },
      error =>{
        console.log(error);
      }
    );
  }

  getUser(userId){
    this._userService.getUser(userId).subscribe(
      response => {
        if(response.status == 'success'){
          this.user = response.user;
          console.log(this.user);
         
          
        }
      },
      error =>{
        console.log(error);
      }
    );
  }
}
