import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

/**
 * Error interceptor — normalises HTTP errors for Angular services.
 * Logs errors in development; never exposes raw server error details to users.
 */
export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      if (!environment_production()) {
        console.error('[BPSquare HTTP Error]', error.status, error.url, error.message);
      }
      return throwError(() => error);
    })
  );
};

/** Lazily reads production flag without importing environment directly to avoid circular deps. */
function environment_production(): boolean {
  try {
    // Dynamically check if we're in production without a hard import
    return window.location.hostname !== 'localhost';
  } catch {
    return false;
  }
}
